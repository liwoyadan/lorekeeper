<?php

namespace App\Providers;

use App\Providers\Socialite\ToyhouseProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
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

        view()->composer('*', function ($view) {
            // Getting the view's name
            $fullName = $view->getName();
            $directory = substr($fullName, 0, strrpos($fullName, '.'));
            View::share('viewName', $directory);

            $viewData = $view->getData();
            View::share('viewData', $viewData);

            // Declaring which views don't have a sidebar in the first place
            // Doing it this way because...yielded and included views apparently
            // can't access what sections are present on the parent layout...?
            // Currently this is just the layout blades, user list, pages blades, auth blades, and widgets.
            // Also excludes admin blades because those have an incredibly long sidebar.
            // This means if you have any sidebars on the front page i.e. featured character, it will display normally.
            $noSidebar = '/((layout.)(.*)|browse.users|(pages.)(.*)|(auth.)(.*))|(widgets.)(.*)|(admin.)(.*)/i';

            if (preg_match_all($noSidebar, $fullName)) {
                View::share('hasSidebar', false);
            } else {
                View::share('hasSidebar', true);
            }
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
