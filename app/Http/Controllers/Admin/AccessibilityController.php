<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme\AccessibilityOverride;
use App\Models\Theme\AccessibilitySetting;
use App\Models\Theme\Theme;
use App\Services\AccessibilityManager;
use Illuminate\Http\Request;

class AccessibilityController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Accessibility Controller
    |--------------------------------------------------------------------------
    |
    | All the stuff for accessibility/alt settings for themes (there's a config)
    | as well as specific overrides and etc.
    |
    */

    /**
     * Shows the settings index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getSettings() {
        return view('admin.accessibility.settings', [
            'settings' => AccessibilitySetting::sorted()->get(),
            'catalog'  => config('lorekeeper.themes.accessibility.settings'),
            'panels'   => config('lorekeeper.themes.accessibility.panels'),
        ]);
    }

    /**
     * Shows the create setting page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateSetting() {
        return view('admin.accessibility.create_edit_setting', [
            'setting' => new AccessibilitySetting,
            'catalog' => config('lorekeeper.themes.accessibility.settings'),
            'panels'  => config('lorekeeper.themes.accessibility.panels'),
        ]);
    }

    /**
     * Shows the create setting page.
     *
     * @param int|null $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditSetting($id) {
        $setting = AccessibilitySetting::find($id);
        if (!$setting) {
            abort(404);
        }

        return view('admin.accessibility.create_edit_setting', [
            'setting' => $setting,
            'catalog' => config('lorekeeper.themes.accessibility.settings'),
            'panels'  => config('lorekeeper.themes.accessibility.panels'),
        ]);
    }

    /**
     * Creates or edits a setting.
     *
     * @param App\Services\AccessibilityManager $service
     * @param int|null                          $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditSetting(Request $request, AccessibilityManager $service, $id = null) {
        $data = $request->all();
        $catalog = config('lorekeeper.themes.accessibility.settings.'.($data['setting_key'] ?? ''));
        if ($catalog) {
            $data['input_type'] = $catalog['input_type'];
            $request->merge(['input_type' => $catalog['input_type']]);
        }
        $request->validate(AccessibilitySetting::$rules);

        $setting = $id ? AccessibilitySetting::find($id) : null;
        if ($id && !$setting) {
            abort(404);
        }

        $setting = $service->createEditSetting($setting, $data);
        if ($id && $setting) {
            flash('Setting updated successfully.')->success();
        } elseif (!$id && $setting) {
            flash('Setting created successfully.')->success();

            return redirect()->to('admin/accessibility-settings/edit/'.$setting->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the setting deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteSetting($id) {
        return view('admin.accessibility._delete_setting', [
            'setting' => AccessibilitySetting::find($id),
        ]);
    }

    /**
     * Deletes a setting.
     *
     * @param App\Services\AccessibilityManager $service
     * @param int                               $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteSetting(AccessibilityManager $service, $id) {
        $setting = AccessibilitySetting::find($id);
        if ($setting && $service->deleteSetting($setting)) {
            flash('Setting deleted successfully.')->success();
        } else {
            flash('Failed to delete setting.')->error();
        }

        return redirect()->to('admin/accessibility-settings');
    }

    /**
     * Gets the overrides.
     */
    public function getOverrides() {
        return view('admin.accessibility.overrides', [
            'catalog'   => config('lorekeeper.themes.accessibility.settings'),
            'overrides' => AccessibilityOverride::get()->keyBy('setting_key'),
        ]);
    }

    /**
     * Saves overrides for a setting.
     *
     * @param App\Services\AccessibilityManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postOverrides(Request $request, AccessibilityManager $service) {
        if ($service->saveOverride($request->input('overrides', []))) {
            flash('Overrides saved successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the overrides for a theme.
     */
    public function getThemeOverrides(Request $request) {
        $themes = Theme::orderBy('name')->get();
        $themeId = $request->input('theme');
        $theme = $themeId ? $themes->firstWhere('id', (int) $themeId) : null;

        return view('admin.accessibility.theme_overrides', [
            'themes'   => $themes,
            'theme'    => $theme,
            'settings' => AccessibilitySetting::sorted()->get(),
        ]);
    }

    /**
     * Saves the overrides for a specified theme.
     *
     * @param App\Services\AccessibilityManager $service
     * @param mixed                             $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postThemeOverrides(Request $request, AccessibilityManager $service, $id) {
        $theme = Theme::find($id);
        if (!$theme) {
            abort(404);
        }

        if ($service->saveThemeOverride($theme, $request->input('overrides', []))) {
            flash('Theme overrides saved successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }
}
