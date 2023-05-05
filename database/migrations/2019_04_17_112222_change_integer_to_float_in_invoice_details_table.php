<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeIntegerToFloatInInvoiceDetailsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('invoice_details', function (Blueprint $table) {
			$platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
			$platform->registerDoctrineTypeMapping('enum', 'string');
			$table->float('discount')->default(0)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('invoice_details', function (Blueprint $table) {
			$table->integer('discount')->default(0);
		});
	}
}
