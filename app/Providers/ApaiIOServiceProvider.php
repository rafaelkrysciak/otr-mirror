<?php namespace App\Providers;

use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use Illuminate\Support\ServiceProvider;
use \Config;

class ApaiIOServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ApaiIO\ApaiIO', function ($app) {
            $conf = new GenericConfiguration();
            $conf->setCountry(Config::get('aws.country'))
                ->setAccessKey(Config::get('aws.pa_key'))
                ->setSecretKey(Config::get('aws.pa_secret'))
                ->setAssociateTag(Config::get('aws.associateTag'))
                ->setRequest('\ApaiIO\Request\Soap\Request')
                ->setResponseTransformer('\ApaiIO\ResponseTransformer\ObjectToArray');

            return new ApaiIO($conf);
        });

    }

}
