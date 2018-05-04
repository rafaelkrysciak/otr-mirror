<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
			'name' => 'rafaelk',
			'email' => 'rafael.krysciak@gmail.com',
			'password' => bcrypt('HqMirrorAdmin'),
			'premium_valid_until' => '2030-01-01',
			'confirmed' => 1
		]);
    }
}
