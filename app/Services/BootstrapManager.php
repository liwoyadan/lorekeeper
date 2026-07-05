<?php

namespace App\Services;

use App\Models\Theme\ThemeBootstrap;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Exception\SassException;
use ScssPhp\ScssPhp\OutputStyle;
use ScssPhp\ScssPhp\ValueConverter;
use Throwable;

class BootstrapManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Bootstrap Manager
    |--------------------------------------------------------------------------
    |
    | Compiles a ThemeBootstrap's data into a Bootstrap CSS file with the
    | data from the Create/Edit Bootstrap Theme in admin panel, using
    | Bootstrap's SCSS files & ScssPhp.
    |
    */

    public function createEditBootstrap($bootstrap, $data) {
        DB::beginTransaction();

        try {
            $isDefault = isset($data['is_default']) && $data['is_default'];

            $bootstrap->fill([
                'name'             => $data['name'] ?? null,
                'color_data'       => $this->setupColorData($data),
                'theme_color_data' => $this->setupThemeColorData($data),
                'style_data'       => $this->setupStyleData($data),
                'custom_scss_data' => $this->setupCustomData($data),
                'custom_prepend'   => ($data['custom_prepend'] ?? null) ?: null,
                'custom_append'    => ($data['custom_append'] ?? null) ?: null,
                'is_default'       => $isDefault,
            ]);
            $bootstrap->save();

            if ($isDefault) {
                ThemeBootstrap::where('id', '!=', $bootstrap->id)
                    ->where('is_default', 1)
                    ->update(['is_default' => 0]);
            }

            return $this->commitReturn($bootstrap);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * The ... compiling :)
     * Should (hopefully) spit out a proper SCSS compile error
     * if anything goes wrong...
     *
     * @param mixed $bootstrap
     */
    public function compile($bootstrap) {
        DB::beginTransaction();

        try {
            $variables = $this->themeVariables($bootstrap);
            $source = $this->buildScssSource($bootstrap);
            $hash = substr(sha1($source.serialize($variables)), 0, 10);

            $compiler = new Compiler;
            $compiler->addImportPath(base_path('resources/sass'));
            $compiler->setOutputStyle(OutputStyle::COMPRESSED);
            $compiler->replaceVariables(array_map(function ($value) {
                return ValueConverter::parseValue($value);
            }, $variables));

            $result = $compiler->compileString($source);

            $outputDir = $bootstrap->imagePath;
            if (!File::isDirectory($outputDir)) {
                File::makeDirectory($outputDir, 0755, true);
            }

            $bootstrap->hash = $hash;
            $newFileName = $bootstrap->compiledFileName;
            File::put($outputDir.'/'.$newFileName, $result->getCss());

            $bootstrap->save();

            $this->cleanOldCompiles($bootstrap, $outputDir, $newFileName);

            return $this->commitReturn(true);
        } catch (SassException $e) {
            $this->setError('error', $e->getOriginalMessage());

            return $this->rollbackReturn(false);
        } catch (Throwable $e) {
            Log::error('Bootstrap theme compilation failed', [
                'bootstrap_id' => $bootstrap->id,
                'exception'    => $e->getMessage(),
            ]);
            $this->setError('error', $e->getMessage());

            return $this->rollbackReturn(false);
        }
    }

    public function deleteFiles($bootstrap) {
        if ($bootstrap->compiledFileName && File::exists($bootstrap->imagePath.'/'.$bootstrap->compiledFileName)) {
            $this->deleteImage($bootstrap->imagePath, $bootstrap->compiledFileName);
        }
    }

    /**
     * Drops blank color_data entries.
     *
     * @param mixed $data
     */
    private function setupColorData($data) {
        $colorData = array_filter($data['color_data'] ?? [], function ($v) {
            return $v != null && $v != '';
        });

        return $colorData ?: null;
    }

    /**
     * Builds per-colour [lighten, step] entries, keeping only colours that have a
     * step or the lighten toggle set.
     *
     * @param mixed $data
     */
    private function setupThemeColorData($data) {
        $input = $data['theme_color_data'] ?? [];
        $themeColorData = [];
        foreach (array_keys(config('lorekeeper.themes.theme_colors')) as $color) {
            $entry = [
                'lighten' => isset($input[$color]['lighten']) ? 1 : 0,
                'step'    => isset($input[$color]['step']) && $input[$color]['step'] != ''
                    ? (int) $input[$color]['step']
                    : null,
            ];
            if ($entry['step'] != null || $entry['lighten']) {
                $themeColorData[$color] = $entry;
            }
        }

        return $themeColorData ?: null;
    }

    /**
     * Drops blank values, then sets every toggleable value to 1 or 0.
     *
     * @param mixed $data
     */
    private function setupStyleData($data) {
        $styleData = array_filter($data['style_data'] ?? [], function ($v) {
            return $v != null && $v != '';
        });
        foreach (array_keys(config('lorekeeper.themes.toggles')) as $toggle) {
            $styleData[$toggle] = (isset($data['style_data'][$toggle]) && $data['style_data'][$toggle]) ? 1 : 0;
        }

        return $styleData ?: null;
    }

    /**
     * Parses the custom theme-color rows and the custom variable overrides.
     * Drops blanks and $-values (SCSS variables are undefined at the point
     * these get injected, so they'd break the whole compile).
     *
     * @param mixed $data
     */
    private function setupCustomData($data) {
        $input = $data['custom_scss_data']['theme_colors'] ?? [];
        $names = $input['name'] ?? [];
        $values = $input['value'] ?? [];
        $steps = $input['step'] ?? [];
        $lightens = $input['lighten'] ?? [];
        $themeColorKeys = array_keys(config('lorekeeper.themes.theme_colors'));

        $additions = [];
        foreach ($names as $i => $rawName) {
            $name = preg_replace('/[^a-z0-9-]/', '', strtolower(trim($rawName ?? '')));
            $value = isset($values[$i]) ? trim($values[$i]) : '';
            if ($name == '' || $value == '' || in_array($name, $themeColorKeys) || preg_match('/^\$/', $value)) {
                continue;
            }
            $additions[$name] = [
                'value'   => $value,
                'step'    => isset($steps[$i]) && $steps[$i] != '' ? (int) $steps[$i] : null,
                'lighten' => isset($lightens[$i]) && $lightens[$i] ? 1 : 0,
            ];
        }

        $varInput = $data['custom_scss_data']['custom_variables'] ?? [];
        $varNames = $varInput['name'] ?? [];
        $varValues = $varInput['value'] ?? [];

        $customVariables = [];
        foreach ($varNames as $i => $rawName) {
            $name = preg_replace('/[^a-z0-9-]/', '', strtolower(trim($rawName ?? '')));
            $value = isset($varValues[$i]) ? trim($varValues[$i]) : '';
            if ($name == '' || $value == '' || preg_match('/^\$/', $value)) {
                continue;
            }
            $customVariables[$name] = $value;
        }

        $custom = [];
        if ($additions) {
            $custom['theme_colors'] = $additions;
        }
        if ($customVariables) {
            $custom['custom_variables'] = $customVariables;
        }

        return $custom ?: null;
    }

    /**
     * Assembles the SCSS with scssphp. Output is a drop-in replacement
     * for the default compiled app.css with all the custom values.
     *
     * @param mixed $bootstrap
     */
    private function buildScssSource($bootstrap) {
        $themeColors = $this->themeColorConfig($bootstrap);

        $sections = [
            $this->customThemeColors($bootstrap),
            $this->themeColorMap($themeColors),
            $themeColors ? "@import 'lk-theme';" : '',
            $bootstrap->custom_prepend ?? '',
            "@import 'app';",
            $themeColors ? '@include lk-theme-generate($lk-theme-colors);' : '',
            $this->styleCssVariables($bootstrap),
            $bootstrap->custom_append ?? '',
        ];

        return implode("\n\n", array_filter($sections, function ($section) {
            return $section != '';
        }))."\n";
    }

    /**
     * it's theme variable time (thumbs up)
     * Does all the sections (colors, styles, typography, etc) on the
     * create/edit page and sets their values with their BS variable
     * names. These will override anything Bootstrap has declared as !default.
     *
     * @param mixed $bootstrap
     */
    private function themeVariables($bootstrap) {
        $colorData = $bootstrap->color_data ?? [];
        $vars = [];

        foreach (['base_colors', 'grays', 'theme_colors'] as $group) {
            foreach (config('lorekeeper.themes.'.$group) as $key => $entry) {
                $vars[$key] = $colorData[$key] ?? $entry['default'];
            }
        }

        $vars += $this->setValues($bootstrap, ['styles'], false);
        $vars += $this->setValues($bootstrap, ['typography'], true);
        $vars += $this->setValues($bootstrap, ['extras', 'tooltips', 'thumbnails'], true);

        $styleData = $bootstrap->style_data ?? [];
        foreach (config('lorekeeper.themes.toggles') as $key => $entry) {
            $vars[$key] = ($styleData[$key] ?? $entry['default']) ? 'true' : 'false';
        }

        foreach ($bootstrap->custom_scss_data['custom_variables'] ?? [] as $name => $value) {
            $vars[$name] = $value;
        }

        return $vars;
    }

    /**
     * Adds in any custom additions to Bootstrap's $theme-colors map.
     * Also will generate the full utility family (.btn-{name}, .bg-{name},
     * .text-{name}, etc.) for every custom theme colour.
     *
     * @param mixed $bootstrap
     */
    private function customThemeColors($bootstrap) {
        $additions = $bootstrap->custom_scss_data['theme_colors'] ?? [];
        if (!$additions) {
            return '';
        }

        $pairs = [];
        foreach ($additions as $name => $entry) {
            $value = $entry['value'] ?? '';
            if ($value == '') {
                continue;
            }
            $pairs[] = '  "'.$name.'": '.$value;
        }

        if (!$pairs) {
            return '';
        }

        return "// --- CUSTOM THEME COLOR ADDITIONS ---\n".'$theme-colors: ('."\n".implode(",\n", $pairs)."\n);";
    }

    /**
     * Handles all the style_data...
     * (the $skipBlank just skips blank values and falls back to BS default).
     *
     * @param mixed $bootstrap
     * @param mixed $groups
     * @param mixed $skipBlank
     */
    private function setValues($bootstrap, $groups, $skipBlank) {
        $styleData = $bootstrap->style_data ?? [];
        $values = [];
        foreach ($groups as $group) {
            foreach (config('lorekeeper.themes.'.$group) as $key => $entry) {
                if ($skipBlank && (!isset($styleData[$key]) || $styleData[$key] == '')) {
                    continue;
                }
                $value = $styleData[$key] ?? $entry['default'];
                if (($entry['type'] ?? null) == 'width' && is_numeric($value)) {
                    $value .= 'px';
                }
                $values[$key] = $value;
            }
        }

        return $values;
    }

    /**
     * Style & typography values as a :root CSS variables
     * Skips blank typography values.
     *
     * @param mixed $bootstrap
     */
    private function styleCssVariables($bootstrap) {
        $declarations = [];
        foreach ($this->setValues($bootstrap, ['styles'], false) + $this->setValues($bootstrap, ['typography'], true) as $key => $value) {
            $declarations[] = '  --'.$key.': '.$value.';';
        }

        return $declarations ? "// --- Style CSS variables ---\n:root {\n".implode("\n", $declarations)."\n}" : '';
    }

    /**
     * Processes theme_color_data into [name => [base, step, direction]] for the
     * colours that have a step and exist in the config file.
     *
     * @param mixed $bootstrap
     */
    private function themeColorConfig($bootstrap) {
        $themeColorData = $bootstrap->theme_color_data ?? [];
        $colorData = $bootstrap->color_data ?? [];
        $themeColors = config('lorekeeper.themes.theme_colors');

        $config = [];
        foreach ($themeColorData as $key => $entry) {
            if (!isset($themeColors[$key])) {
                continue;
            }
            $step = (int) ($entry['step'] ?? 0);
            if ($step <= 0) {
                continue;
            }
            $config[$key] = [
                'base'      => $colorData[$key] ?? $themeColors[$key]['default'],
                'step'      => $step,
                'direction' => isset($entry['lighten']) && $entry['lighten'] ? 'lighten' : 'darken',
            ];
        }

        foreach ($bootstrap->custom_scss_data['theme_colors'] ?? [] as $name => $entry) {
            if (($entry['value'] ?? '') == '') {
                continue;
            }
            $step = (int) ($entry['step'] ?? 0);
            if ($step <= 0) {
                continue;
            }
            $config[$name] = [
                'base'      => $entry['value'],
                'step'      => $step,
                'direction' => isset($entry['lighten']) && $entry['lighten'] ? 'lighten' : 'darken',
            ];
        }

        return $config;
    }

    /**
     * Makes a map of the theme colours as the $lk-theme-colors SCSS map.
     *
     * @param mixed $config
     */
    private function themeColorMap($config) {
        if (!$config) {
            return '';
        }

        $pairs = [];
        foreach ($config as $name => $entry) {
            $pairs[] = '  "'.$name.'": (base: '.$entry['base'].', step: '.$entry['step'].', direction: "'.$entry['direction'].'")';
        }

        return "// --- THEME COLOR MAP ---\n".'$lk-theme-colors: ('."\n".implode(",\n", $pairs)."\n);";
    }

    /**
     * Deletes any old {id}-{hash}.css files for this Bootstrap
     * using regex.
     *
     * @param mixed $bootstrap
     * @param mixed $outputDir
     * @param mixed $keepFileName
     */
    private function cleanOldCompiles($bootstrap, $outputDir, $keepFileName) {
        foreach (File::glob($outputDir.'/'.$bootstrap->id.'-*.css') as $stale) {
            $base = basename($stale);
            if ($base == $keepFileName) {
                continue;
            }
            if (!preg_match('/^'.preg_quote($bootstrap->id, '/').'-[a-f0-9]+\.css$/', $base)) {
                continue;
            }
            File::delete($stale);
        }
    }
}
