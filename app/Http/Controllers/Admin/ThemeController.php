<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Theme\Theme;
use App\Models\Theme\ThemeBootstrap;
use App\Models\User\User;
use App\Services\BootstrapManager;
use App\Services\ThemeManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use stdClass;

class ThemeController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin / Theme Controller
    |--------------------------------------------------------------------------
    |
    | Handles creation/editing of theme categories and themes.
    |
    */

    /**
     * Shows the theme index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request) {
        $query = Theme::query();
        $data = $request->only(['name', 'sort']);

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortNewest(1);
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(1);
                    break;
                case 'alpha':
                    $query->sortAlphabetical();
                    break;
            }
        } else {
            $query->sortNewest(1);
        }

        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        return view('admin.themes.themes', [
            'siteThemes' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the bootstrap themes index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getBootstrapIndex(Request $request) {
        $query = ThemeBootstrap::withCount('themes');
        $data = $request->only(['name', 'sort']);

        if (isset($data['name'])) {
            $query->where('name', 'LIKE', '%'.$data['name'].'%');
        }

        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'newest':
                    $query->sortNewest();
                    break;
                case 'oldest':
                    $query->sortNewest(1);
                    break;
                case 'alpha-reverse':
                    $query->sortAlphabetical(1);
                    break;
                default:
                    $query->sortAlphabetical();
                    break;
            }
        } else {
            $query->sortAlphabetical();
        }

        return view('admin.themes.bootstrap_themes', [
            'bootstrapThemes' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the create bootstrap theme page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateBootstrapTheme() {
        return view('admin.themes.create_edit_bootstrap_theme', [
            'bootstrap'      => new ThemeBootstrap,
        ] + $this->bootstrapThemeViewData());
    }

    /**
     * Shows the edit bootstrap theme page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditBootstrapTheme($id) {
        $bootstrap = ThemeBootstrap::find($id);
        if (!$bootstrap) {
            abort(404);
        }

        return view('admin.themes.create_edit_bootstrap_theme', [
            'bootstrap'      => $bootstrap,
        ] + $this->bootstrapThemeViewData());
    }

    /**
     * Creates or edits a bootstrap theme.
     *
     * @param int|null $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditBootstrapTheme(Request $request, $id = null) {
        $request->validate(ThemeBootstrap::$rules);

        $bootstrap = $id ? ThemeBootstrap::find($id) : new ThemeBootstrap;
        if (!$bootstrap) {
            abort(404);
        }

        $manager = new BootstrapManager;
        if (!$manager->createEditBootstrap($bootstrap, $request->all())) {
            foreach ($manager->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }

            return redirect()->back();
        }

        $message = 'Bootstrap theme '.($id ? 'updated' : 'created').' successfully.';

        if ($request->input('action') == 'save_compile') {
            if ($manager->compile($bootstrap)) {
                flash($message.' Compiled successfully.')->success();
            } else {
                $errors = $manager->errors()->getMessages()['error'] ?? ['Compilation failed.'];
                flash($message.' Compilation failed: '.implode(' ', $errors))->error();
            }
        } else {
            flash($message)->success();
        }
        ThemeBootstrap::clearDefaultCache();

        return redirect()->to('admin/bootstrap-themes/edit/'.$bootstrap->id);
    }

    /**
     * Gets the bootstrap theme deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteBootstrapTheme($id) {
        $bootstrap = ThemeBootstrap::withCount('themes')->find($id);

        return view('admin.themes._delete_bootstrap_theme', [
            'bootstrap' => $bootstrap,
        ]);
    }

    /**
     * Deletes a bootstrap theme.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteBootstrapTheme(Request $request, $id) {
        $bootstrap = ThemeBootstrap::withCount('themes')->find($id);
        if (!$bootstrap) {
            abort(404);
        }
        if ($bootstrap->themes_count > 0) {
            flash('Cannot delete a bootstrap theme that is in use by one or more themes.')->error();

            return redirect()->back();
        }

        (new BootstrapManager)->deleteFiles($bootstrap);

        $bootstrap->delete();
        ThemeBootstrap::clearDefaultCache();
        flash('Bootstrap theme deleted successfully.')->success();

        return redirect()->to('admin/bootstrap-themes');
    }

    /**
     * Shows the create theme page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateTheme() {
        $conditions = new stdClass;
        if (class_exists('\App\Models\Weather\WeatherSeason')) {
            $conditions->seasons = \App\Models\Weather\WeatherSeason::get()->pluck('name', 'id');
            $conditions->weathers = \App\Models\Weather\Weather::get()->pluck('name', 'id');
        }

        return view('admin.themes.create_edit_theme', [
            'theme'             => new Theme,
            'conditions'        => $conditions,
            'userOptions'       => User::orderBy('name', 'ASC')->pluck('name', 'id')->toArray(),
            'bootstrapOptions'  => ThemeBootstrap::orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Shows the edit theme page.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getEditTheme($id) {
        $conditions = new stdClass;
        if (class_exists('\App\Models\Weather\WeatherSeason')) {
            $conditions->seasons = \App\Models\Weather\WeatherSeason::get()->pluck('name', 'id');
            $conditions->weathers = \App\Models\Weather\Weather::get()->pluck('name', 'id');
        }

        $theme = Theme::find($id);
        if (!$theme) {
            abort(404);
        }

        return view('admin.themes.create_edit_theme', [
            'theme'             => $theme,
            'conditions'        => $conditions,
            'userOptions'       => User::orderBy('name', 'ASC')->pluck('name', 'id')->toArray(),
            'bootstrapOptions'  => ThemeBootstrap::orderBy('name')->pluck('name', 'id')->toArray(),
        ]);
    }

    /**
     * Creates or edits an theme.
     *
     * @param App\Services\ThemeManager $service
     * @param int|null                  $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateEditTheme(Request $request, ThemeManager $service, $id = null) {
        $id ? $request->validate(Theme::$updateRules) : $request->validate(Theme::$createRules);
        $data = $request->all();

        if ($id && $service->updateTheme(Theme::find($id), $data, Auth::user())) {
            flash('Theme updated successfully.')->success();
        } elseif (!$id && $theme = $service->createTheme($data, Auth::user())) {
            flash('Theme created successfully.')->success();

            return redirect()->to('admin/themes/edit/'.$theme->id);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->back();
    }

    /**
     * Gets the theme deletion modal.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getDeleteTheme($id) {
        $theme = Theme::find($id);

        return view('admin.themes._delete_theme', [
            'theme' => $theme,
        ]);
    }

    /**
     * Creates or edits an theme.
     *
     * @param App\Services\ThemeManager $service
     * @param int                       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteTheme(Request $request, ThemeManager $service, $id) {
        if ($id && $service->deleteTheme(Theme::find($id))) {
            flash('Theme deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                flash($error)->error();
            }
        }

        return redirect()->to('admin/themes');
    }

    /**
     * Shared default data for the Bootstrap theme create/edit view,
     * editable in config. Just a private function for ease of edit.
     *
     * @return array
     */
    private function bootstrapThemeViewData() {
        return [
            'baseColors'   => config('lorekeeper.themes.base_colors'),
            'grays'        => config('lorekeeper.themes.grays'),
            'themeColors'  => config('lorekeeper.themes.theme_colors'),
            'styles'       => config('lorekeeper.themes.styles'),
            'borderStyles' => config('lorekeeper.themes.border_styles'),
            'typography'   => config('lorekeeper.themes.typography'),
            'toggles'      => config('lorekeeper.themes.toggles'),
            'extras'       => config('lorekeeper.themes.extras'),
            'tooltips'     => config('lorekeeper.themes.tooltips'),
            'thumbnails'   => config('lorekeeper.themes.thumbnails'),
            'stepDefault'  => config('lorekeeper.themes.theme_color_step_default'),
        ];
    }
}
