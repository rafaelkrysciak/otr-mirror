<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaypalTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('paypal_transactions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned()->index();
			$table->integer('payment_product_id')->unsigned()->index();
			$table->string('email');
			$table->string('payerid', 30);
			$table->string('firstname', 50);
			$table->string('lastname', 50);
			$table->string('countrycode', 2);
			$table->string('payerstatus', 30);
			$table->string('transactionid', 30)->index();
			$table->string('transactiontype', 30);
			$table->string('paymenttype', 30);
			$table->timestamp('ordertime');
			$table->float('amt');
			$table->float('feeamt');
			$table->string('currencycode', 3);
			$table->string('paymentstatus', 30);
			$table->string('pendingreason', 30);
			$table->timestamps();

			$table->foreign('user_id')
				->references('id')
				->on('users');

			$table->foreign('payment_product_id')
				->references('id')
				->on('payment_products');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('paypal_transactions');
	}

}
