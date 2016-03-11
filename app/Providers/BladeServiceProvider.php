<?php namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Blade;

class BladeServiceProvider  extends ServiceProvider {

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Blade::extend(function($value, $compiler)
        {
            $pattern = $compiler->createMatcher('byteToSize');
            $replace = '$1<?php echo byteToSize$2; ?>';

            return preg_replace($pattern, $replace, $value);
        });
    }

    public function map() {}
}