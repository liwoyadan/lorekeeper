<?php

namespace App\Providers;

use App\Facades\Settings;
use App\Models\Theme\Theme;
use App\Providers\Socialite\ToyhouseProvider;
use App\Services\AccessibilityManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register() {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() {
        //
        Schema::defaultStringLength(191);
        Paginator::defaultView('layouts._pagination');
        Paginator::defaultSimpleView('layouts._simple-pagination');
        // Add any other views that require the theme variables below aka anything with tinymce initialization
        $composerViews = ['layouts.app', 'account.settings', 'account.accessibility', 'character._image_js', 'comments._perma_layout', 'comments.comments', 'js._modal_wysiwyg', 'js._tinymce_wysiwyg'];

        view()->composer($composerViews, function ($view) {
            $theme = Auth::user()->theme ?? Theme::where('is_default', true)->first() ?? null;
            $conditionalTheme = null;
            if (class_exists('\App\Models\Weather\WeatherSeason')) {
                $conditionalTheme = Theme::where('link_type', 'season')->where('link_id', Settings::get('site_season'))->first() ??
                Theme::where('link_type', 'weather')->where('link_id', Settings::get('site_weather'))->first() ??
                $theme;
            }
            $decoratorTheme = Auth::user()->decoratorTheme ?? null;
            View::share('theme', $theme);
            View::share('conditionalTheme', $conditionalTheme);
            View::share('decoratorTheme', $decoratorTheme);

            try {
                $accessibility = new AccessibilityManager;
                View::share('a11yStyleBlock', $accessibility->compileStyleBlock(Auth::user(), $theme));
                View::share('a11yClientMap', $accessibility->activeSettingsArray());
            } catch (\Exception $e) {
                View::share('a11yStyleBlock', '');
                View::share('a11yClientMap', []);
            }
        });

        view()->composer('account._accessibility_panel', function ($view) {
            $accessibility = new AccessibilityManager;
            $user = Auth::user();
            $theme = $user?->theme ?? (Theme::where('is_default', true)->first() ?? null);

            $grouped = $accessibility->panelSettings($theme);
            $panels = config('lorekeeper.themes.accessibility.panels');
            $order = collect(array_keys($panels))->filter(function ($key) use ($grouped) {
                return $grouped->has($key);
            })->merge($grouped->keys()->diff(array_keys($panels)))->values();

            $panelData = [];
            foreach ($order as $panelKey) {
                $settings = [];
                foreach ($grouped[$panelKey] as $setting) {
                    $settings[] = [
                        'setting' => $setting,
                        'value'   => $accessibility->displayValue($setting, $user),
                        'default' => $accessibility->defaultValue($setting, $theme),
                        'options' => $accessibility->getOptionSet($setting, $theme),
                    ];
                }
                $panelData[] = ['label' => $panels[$panelKey] ?? ucfirst($panelKey), 'settings' => $settings];
            }

            $view->with('a11yPanelData', $panelData);
            $view->with('a11yIsAuth', Auth::check());
            $view->with('a11ySaved', $user && $user->settings ? ($user->settings->accessibility_data ?? []) : []);
            $view->with('a11yClientMap', $accessibility->activeSettingsArray());
        });

        /*
         * Paginate a standard Laravel Collection.
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         * @return array
         */
        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page') {
            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);

            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path'     => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        $this->bootToyhouseSocialite();
    }

    /**
     * Boot Toyhouse Socialite provider.
     */
    private function bootToyhouseSocialite() {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend(
            'toyhouse',
            function ($app) use ($socialite) {
                $config = $app['config']['services.toyhouse'];

                return $socialite->buildProvider(ToyhouseProvider::class, $config);
            }
        );
    }
}
