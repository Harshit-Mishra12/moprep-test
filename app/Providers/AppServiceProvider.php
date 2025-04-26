<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;

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
    public function boot(){
		
        Schema::defaultStringLength(191);

        View::composer('layouts.app', function($view){   

            $courses = \App\Models\Course::where('status','1')->where('deleted_at',NULL)->orderBy('sort_order','ASC')->get();
            return $view->with(['courses'=>$courses]);

        });
    }
}
