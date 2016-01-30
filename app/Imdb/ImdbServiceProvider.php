<?php namespace App\Imdb;


use Illuminate\Support\ServiceProvider;

class ImdbServiceProvider extends ServiceProvider{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register new instance of imdb class
     *
     * @return \imdb
     */
    public function register()
    {
        $this->app->bindShared('App\Imdb\Imdb', function($app)
        {
            $imdbConfig = new \Imdb\Config(base_path().'/config/imdb.ini');
            $imdbConfig->cachedir = storage_path().'/imdb_cache/';
            $imdbConfig->photodir = public_path().'/imdb/';
            return new Imdb($imdbConfig);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['App\Imdb\Imdb'];
    }
}