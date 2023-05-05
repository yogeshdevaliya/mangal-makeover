<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeauticianIdInInvoiceDetailsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('invoice_details', function (Blueprint $table) {
			$table->string('beautician_id')->nullable()->after('name');
		});

		Schema::table('running_services', function (Blueprint $table) {
			$table->string('beautician_id')->nullable()->after('item_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('invoice_details', function (Blueprint $table) {
			$table->dropColumn('beautician_id');
		});

		Schema::table('running_services', function (Blueprint $table) {
			$table->dropColumn('beautician_id');
		});
	}
}
