<?php

use Illuminate\Database\Seeder;

class PaymentProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_products')->insert([
            'name' => '1 Monat Premium',
            'description' => 'Ein Monat Premium Mitgliedschaft',
            'price' => 2.5,
			'premium_months' => 1,
			'valid_until' => '2030-01-01',
			'created_at' => DB::raw('NOW()'),
			'updated_at' => DB::raw('NOW()'),
        ]);
        DB::table('payment_products')->insert([
            'name' => '6 Monat Premium',
            'description' => 'Sechs Monate Premium Mitgliedschaft',
            'price' => 10.5,
			'premium_months' => 6,
			'valid_until' => '2030-01-01',
			'created_at' => DB::raw('NOW()'),
			'updated_at' => DB::raw('NOW()'),
        ]);
        DB::table('payment_products')->insert([
            'name' => '12 Monat Premium',
            'description' => 'Ein Jahr Premium Mitgliedschaft',
            'price' => 15,
			'premium_months' => 12,
			'valid_until' => '2030-01-01',
			'created_at' => DB::raw('NOW()'),
			'updated_at' => DB::raw('NOW()'),
		]);
    }
}
