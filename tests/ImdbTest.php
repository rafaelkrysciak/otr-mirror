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
            ->see('Robert De Niro')
            ->see('Travis Bickle')
            ->see('option value="16" selected')
            ->see('1976')
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


    public function testNoAccess()
    {
        $imdbService = new \App\Services\ImdbService();
        $id = $imdbService->getSeriesIdIfEpisode('5973910');
        $this->assertEquals('0898266', $id);
    }
}
