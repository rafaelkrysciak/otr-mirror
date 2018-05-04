<?php

use Illuminate\Database\Seeder;

class FilmsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		
        DB::table('films')->insert([
			"id" => 0,
			"title" => "Dummy",
			"original_title" => NULL,
			"description" => NULL,
			"year" => NULL,
			"country" => NULL,
			"genre" => NULL,
			"director" => NULL,
			"fsk" => NULL,
			"amazon_asin" => NULL,
			"amazon_link" => NULL,
			"amazon_image" => NULL,
			"imdb_last_update" => "0000-00-00 00:00:00",
			"imdb_id" => "",
			"imdb_rating" => NULL,
			"imdb_votes" => NULL,
			"imdb_runtime" => NULL,
			"imdb_image" => NULL,
			"trailer" => NULL,
			"dvdkritik" => NULL,
			"tvseries" => 0,
			"created_at" => NULL,
			"updated_at" => NULL,
		]);
    }
}
