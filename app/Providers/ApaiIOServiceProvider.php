<?php namespace App\Providers;

use ApaiIO\ApaiIO;
use ApaiIO\Configuration\GenericConfiguration;
use Illuminate\Support\ServiceProvider;
use \ApaiIO\Request\Rest\Request;
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
            $request = new Request();
            $conf = new GenericConfiguration();
            $conf->setCountry(Config::get('aws.country'))
                ->setAccessKey(Config::get('aws.pa_key'))
                ->setSecretKey(Config::get('aws.pa_secret'))
                ->setAssociateTag(Config::get('aws.associateTag'))
                ->setRequest($request)
                ->setResponseTransformer('\ApaiIO\ResponseTransformer\XmlToSimpleXmlObject');

            return new ApaiIO($conf);
        });

    }

}
