<?php

namespace App\Services;

use App\Models\Theme\AccessibilityOverride;
use App\Models\Theme\AccessibilitySetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AccessibilityManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Accessibility Manager
    |--------------------------------------------------------------------------
    |
    | Edits & applies personal user accessibility preferences, which can be
    | defined in the admin panel. Settings only take effect when a value is set.
    | Otherwise it leaves the theme default for the given setting alone.
    |
    */

    /**
     * Settings definitions, cached to prevent repetitive queries...
     */
    public function definitions() {
        return Cache::rememberForever('accessibility_definitions', function () {
            return AccessibilitySetting::active()->sorted()->get();
        });
    }

    public static function clearDefinitionsCache() {
        Cache::forget('accessibility_definitions');
    }

    /**
     * Active accessibility/alt settings the panel should show, grouped.
     * Inactive ones are hidden. The function just consolidates the
     * settings grab & grouping in one place for convenience.
     */
    public function panelSettings($theme) {
        return $this->definitions()->filter(function ($setting) use ($theme) {
            return !($theme && $this->themeDisabled($setting, $theme));
        })->groupBy('panel_key');
    }

    /**
     * Style block for a logged-in users 
     * (this doesn't affect guests, they use localStorage).
     * 
     * @return string
     */
    public function compileStyleBlock($user, $theme) {
        if (!$user || !$theme) {
            return '';
        }

        $rules = [];
        foreach ($this->definitions() as $setting) {
            $value = $this->getValue($setting, $user, $theme);
            if (!isset($value)) {
                continue;
            }
            $css = $this->cssFor($setting, $value);
            if ($css != '') {
                $rules[] = $css;
            }
        }

        if (!count($rules)) {
            return '';
        }

        return '<style id="user-a11y-settings">'.implode('', $rules).'</style>';
    }

    /**
     * The formatted value for this user for a given setting,
     * returns null if unset or disabled.
     * 
     * @return mixed
     */
    public function getValue($setting, $user = null, $theme = null) {
        if (!$user) {
            return null;
        }

        $stored = $this->storedValues($user);
        if (!isset($stored[$setting->setting_key])) {
            return null;
        }
        if ($theme && $this->themeDisabled($setting, $theme)) {
            return null;
        }

        return $this->formatValue($setting, $theme, $stored[$setting->setting_key]);
    }

    /**
     * The user's saved value for a given setting, or null if not applicable.
     * 
     * @return string
     */
    public function displayValue($setting, $user) {
        $stored = $this->storedValues($user);

        return $stored[$setting->setting_key] ?? null;
    }

    /**
     * The admin-set default for a setting, shown as the control's placeholder rather
     * than a pre-selected value. A theme may override the setting's own default.
     */
    public function defaultValue($setting, $theme = null) {
        if ($theme) {
            $override = $this->themeOverride($setting, $theme);
            if ($override && isset($override['default_value']) && $override['default_value'] != '') {
                return $override['default_value'];
            }
        }

        return $setting->default_value ?? null;
    }

    /**
     * The actual CSS for an applied setting,
     * or '' (empty string) if nothing.
     * 
     * @return string
     */
    public function cssFor($setting, $value) {
        $target = $setting->target;
        if (!$target || !isset($target['selector']) || !isset($target['property'])) {
            return '';
        }

        if ($setting->input_type == 'toggle') {
            $token = $this->toggleToken($setting, $value, $target);
            if (!isset($token) || $token == '') {
                return '';
            }
            $extra = $target['property'].': '.$token.' !important;';
            if (isset($target['extra_property']) && $target['extra_property']) {
                $extra .= ' '.$target['extra_property'].': '.$token.' !important;';
            }

            return $target['selector'].' { '.$extra.' }';
        }

        $rendered = $value;
        if (isset($target['unit']) && $target['unit'] && is_numeric($value)) {
            $rendered = $value.$target['unit'];
        }

        return $target['selector'].' { '.$target['property'].': '.$rendered.' !important; }';
    }

    /**
     * Array of the selectors/properties/types/etc of active settings.
     * It's like the function above but for JS-friendliness...
     * 
     * @return array
     */
    public function activeSettingsArray() {
        $map = [];
        foreach ($this->definitions() as $setting) {
            $target = $setting->target;
            if (!$target) {
                continue;
            }
            $options = $setting->options_data ?? [];
            $map[$setting->setting_key] = [
                'selector'       => $target['selector'] ?? null,
                'property'       => $target['property'] ?? null,
                'extra_property' => $target['extra_property'] ?? null,
                'unit'           => $target['unit'] ?? null,
                'input_type'     => $setting->input_type,
                'on_value'       => $options['on_value'] ?? ($target['on_value'] ?? null),
                'off_value'      => $options['off_value'] ?? ($target['off_value'] ?? null),
            ];
        }

        return $map;
    }

    /**
     * Merged alt options set for a setting, with any per-theme 
     * override applied over the global, if applicable.
     * 
     * @return array
     */
    public function getOptionSet($setting, $theme = null) {
        $options = $setting->options_data ?? [];
        if ($theme) {
            $override = $this->themeOverride($setting, $theme);
            if ($override && isset($override['options_data']) && is_array($override['options_data'])) {
                $options = array_merge($options, $override['options_data']);
            }
        }

        return $options;
    }

    /**
     * Validate and/or clamp a value for a given setting (if needed). 
     * If it's invalid (i.e. out of range) then this just returns null.
     * 
     * @return string|null
     */
    public function formatValue($setting, $theme, $value) {
        $options = $this->getOptionSet($setting, $theme);
        $type = $setting->input_type;

        switch ($type) {
            case 'range': case 'number':
                if (!is_numeric($value)) {
                    return null;
                }
                $num = $value + 0;
                if (isset($options['min']) && $num < $options['min']) {
                    $num = $options['min'] + 0;
                }
                if (isset($options['max']) && $num > $options['max']) {
                    $num = $options['max'] + 0;
                }

                return (string) $num;
            case 'toggle':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? '1' : '0';
            case 'select': case 'color':
                if ($type == 'color' && !$setting->is_constrained) {
                    return $value;
                }
                $allowed = $this->allowedValues($options);
                if (count($allowed) && !in_array($value, $allowed)) {
                    return null;
                }

                return $value;
            default:
                return $value ?? null;
        }
    }

    /**
     * Check & store a setting value in the user's accessibility_data!
     * Spits out an error if it isn't valid.
     */
    public function saveUserValue($user, $setting, $value) {
        DB::beginTransaction();
        try {
            $settings = $user->settings;
            if (!$settings) {
                throw new \Exception('Your user settings data could not be retrieved.');
            }

            $clean = $this->formatValue($setting, $user->theme, $value);
            if (!isset($clean)) {
                throw new \Exception('The given value is not a valid value for this setting.');
            }

            $data = $settings->accessibility_data ?? [];
            $data[$setting->setting_key] = $clean;
            $settings->update(['accessibility_data' => $data]);

            return $this->commitReturn($clean);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Removes one setting from the user's accessibility_data.
     */
    public function resetUserValue($user, $setting) {
        DB::beginTransaction();
        try {
            $settings = $user->settings;
            if ($settings) {
                $data = $settings->accessibility_data ?? [];
                unset($data[$setting->setting_key]);
                $settings->update(['accessibility_data' => count($data) ? $data : null]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Clear all of the user's accessibility choices at once.
     * (Nulls the entire column/full reset)
     */
    public function resetAllUserValues($user) {
        DB::beginTransaction();
        try {
            $settings = $user->settings;
            if ($settings) {
                $settings->update(['accessibility_data' => null]);
            }

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Create or update a setting. The entry sets input_type, so a
     * setting can only point at a target that exists in the config.
     */
    public function createEditSetting($setting, $data) {
        DB::beginTransaction();
        try {
            if (!config('lorekeeper.themes.accessibility.settings.'.($data['setting_key'] ?? ''))) {
                throw new \Exception('The selected target does not exist.');
            }

            $data = $this->processSettingData($data);

            if ($setting && $setting->id) {
                $setting->update($data);
            } else {
                $setting = AccessibilitySetting::create($data);
            }

            self::clearDefinitionsCache();

            return $this->commitReturn($setting);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Delete a setting and drop the cached list.
     */
    public function deleteSetting($setting) {
        DB::beginTransaction();
        try {
            $setting->delete();
            self::clearDefinitionsCache();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Save per-site selector/property overrides. $data is keyed by setting_key
     * with 'selector'/'property'; empty rows are removed.
     */
    public function saveOverride($data) {
        DB::beginTransaction();
        try {
            foreach ($data as $key => $fields) {
                if (!config('lorekeeper.themes.accessibility.settings.'.$key)) {
                    continue;
                }

                $selector = isset($fields['selector']) && $fields['selector'] != '' ? $fields['selector'] : null;
                $property = isset($fields['property']) && $fields['property'] != '' ? $fields['property'] : null;
                $existing = AccessibilityOverride::where('setting_key', $key)->first();

                if (!$selector && !$property) {
                    if ($existing) {
                        $existing->delete();
                    }
                    continue;
                }

                if ($existing) {
                    $existing->update(['selector' => $selector, 'property' => $property]);
                } else {
                    AccessibilityOverride::create(['setting_key' => $key, 'selector' => $selector, 'property' => $property]);
                }
            }

            AccessibilityOverride::clearCache();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Save a theme's per-setting overrides into accessibility_data. $data is
     * keyed by setting_key with its is_enabled/default_value/options_data.
     */
    public function saveThemeOverride($theme, $data) {
        DB::beginTransaction();
        try {
            $clean = [];
            foreach ($data as $key => $fields) {
                if (!config('lorekeeper.themes.accessibility.settings.'.$key)) {
                    continue;
                }

                $entry = [];
                if (isset($fields['is_enabled'])) {
                    $entry['is_enabled'] = (bool) $fields['is_enabled'];
                }
                if (isset($fields['default_value']) && $fields['default_value'] != '') {
                    $entry['default_value'] = $fields['default_value'];
                }
                if (isset($fields['options_data']) && is_array($fields['options_data'])) {
                    $entry['options_data'] = $fields['options_data'];
                }
                if (count($entry)) {
                    $clean[$key] = $entry;
                }
            }

            $theme->update(['accessibility_data' => count($clean) ? $clean : null]);

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    private function storedValues($user) {
        return $user && $user->settings ? ($user->settings->accessibility_data ?? []) : [];
    }

    private function themeOverride($setting, $theme) {
        $data = $theme->accessibility_data ?? [];

        return $data[$setting->setting_key] ?? null;
    }

    private function themeDisabled($setting, $theme) {
        $override = $this->themeOverride($setting, $theme);

        return $override && isset($override['is_enabled']) && !$override['is_enabled'];
    }

    private function toggleToken($setting, $value, $target) {
        $options = $setting->options_data ?? [];
        $on = $options['on_value'] ?? ($target['on_value'] ?? null);
        $off = $options['off_value'] ?? ($target['off_value'] ?? null);

        return filter_var($value, FILTER_VALIDATE_BOOLEAN) ? $on : $off;
    }

    private function allowedValues($options) {
        $values = [];
        foreach (['choices', 'presets'] as $bucket) {
            if (isset($options[$bucket]) && is_array($options[$bucket])) {
                foreach ($options[$bucket] as $option) {
                    $values[] = is_array($option) ? ($option['value'] ?? null) : $option;
                }
            }
        }

        return array_values(array_filter($values, function ($value) {
            return isset($value) && $value != '';
        }));
    }

    private function processSettingData($data) {
        $data['is_constrained'] = isset($data['is_constrained']) && $data['is_constrained'];
        $data['is_active'] = isset($data['is_active']) && $data['is_active'];
        $data['sort_order'] = $data['sort_order'] ?? 0;
        if (!isset($data['options_data'])) {
            $data['options_data'] = null;
        }

        return $data;
    }
}
