<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscountAndTotalPriceInRunningServicesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('running_services', function (Blueprint $table) {
			$table->integer('discount')->default(0)->after('name');
			$table->integer('quantity')->default(0)->after('discount');
			$table->float('total_price')->default(0)->after('price');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('running_services', function (Blueprint $table) {
			$table->dropColumn('discount');
			$table->dropColumn('quantity');
			$table->dropColumn('total_price');
		});
	}
}
