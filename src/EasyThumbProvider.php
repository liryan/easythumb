<?php
namespace EasyThumb;
use \Illuminate\Support\ServiceProvider;

class EasyThumbProvider extends ServiceProvider{
    public function register()
    {
        $this->app->singleton('easythumb', function () {
             return new EasyFile();
        });
    }
}
