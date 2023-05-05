<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::create('invoice_details', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('invoice_id')->unsigned();
			$table->foreign('invoice_id')->references('id')->on('invoice')->onDelete('cascade');
			$table->integer('item_id')->default(0);
			$table->string('name')->nullable();
			$table->integer('discount')->default(0);
			$table->integer('quantity')->default(0);
			$table->float('price')->nullable();
			$table->float('total_price')->nullable();
			$table->enum('item_type', ['SERVICE', 'PRODUCT', 'PACKAGE'])->default('SERVICE');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::dropIfExists('invoice_details');
	}
}
