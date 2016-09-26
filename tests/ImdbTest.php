<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ImdbTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testMovie()
    {
        $user = \App\User::find(2);

        \App\Film::where('imdb_id','=','0075314')->delete();

        $this->actingAs($user)
            ->visit('/film/create')
            ->type('tt0075314', 'imdb_id')
            ->press('Create')
            ->see('Taxi Driver')
            ->see('name="country" type="text" value="USA"')
            ->see('Robert De Niro')
            ->see('Travis Bickle')
            ->see('option value="16" selected')
            ->see('name="year" type="text" value="1976"')
            ->see('<input id="tvseries" name="tvseries" type="checkbox" value="1">');
    }


    public function testTvSeries()
    {
        $user = \App\User::find(2);

        \App\Film::where('imdb_id','=','0098864')->delete();

        $this->actingAs($user)
            ->visit('/film/create')
            ->type('tt0098864', 'imdb_id')
            ->press('Create')
            ->see('Molloy')
            ->see('Mayim Bialik')
            ->see('Molloy Martin')
            ->see('1989')
            ->see('input id="tvseries" checked name="tvseries" type="checkbox" value="1"');
    }


    public function testSeriesFromEpisode()
    {
        $imdbService = new \App\Services\ImdbService();

        $id = $imdbService->getSeriesIdIfEpisode('5973910');
        $this->assertEquals('0898266', $id);

        $id = $imdbService->getSeriesIdIfEpisode('5521890');
        $this->assertEquals('0413573', $id);

        $id = $imdbService->getSeriesIdIfEpisode('5257552');
        $this->assertEquals('2741602', $id);

    }


    public function testGoogleSearchSeries()
    {
        $imdbService = new \App\Services\ImdbService();
        $episode = $imdbService->searchWithGoogle('The Big Bang Theory The Conjugal Conjecture');
        $this->assertEquals('3603372', $episode);
        $series = $imdbService->getSeriesIdIfEpisode($episode);
        $this->assertEquals('0898266', $series);
    }

    public function testGoogleSearchSeriesFromEpisode()
    {
        $imdbService = new \App\Services\ImdbService();
        $episode = $imdbService->searchWithGoogle('Empire: Light in Darkness');
        $this->assertEquals('5506788', $episode);
        $series = $imdbService->getSeriesIdIfEpisode($episode);
        $this->assertEquals('3228904', $series);
    }

    public function testIsNotSerial()
    {
        $imdb = \App\Imdb\Imdb::factory('0348656');
        $this->assertFalse($imdb->is_serial());

        $imdb = \App\Imdb\Imdb::factory('0216817');
        $this->assertFalse($imdb->is_serial());
    }

}
