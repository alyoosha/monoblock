<?php

namespace App\Providers;

use App\Models\Configuration;
use App\Models\Section;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::share('isHome', false); // Подключаем переменную языка ко всем шаблонам
        View::share('hasFilter', false); // Подключаем переменную языка ко всем шаблонам

        View::composer('parts.modals', function($view) {
            $componentsConfig = Configuration::where('token_configuration', Session::get('pc-constructor')[0])
                ->leftJoin('components', 'components.id', 'configurations.component_id')
                ->get()
                ->pluck(null, 'section_id');

            $sections = Section::where('is_visible', 1)
                ->get()
                ->groupBy('required');

            $view->with([
                'sectionsAll' => $sections,
                'configuration' => $componentsConfig
            ]);
        });
    }
}
