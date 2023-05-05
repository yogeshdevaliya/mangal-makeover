<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeauticianIdInRunningServicesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('running_services', function (Blueprint $table) {
			$table->integer('beautician_id')->unsigned()->nullable()->after('service_id');
			$table->foreign('beautician_id')->references('id')->on('employees')->onDelete('cascade');
		});

		Schema::table('invoice_details', function (Blueprint $table) {
			$table->integer('beautician_id')->unsigned()->nullable()->after('name');
			$table->foreign('beautician_id')->references('id')->on('employees')->onDelete('cascade');
		});

		Schema::table('invoice', function (Blueprint $table) {
			DB::statement("ALTER TABLE invoice CHANGE COLUMN payment_type payment_type ENUM('CASH', 'ONLINE', 'CARD', 'DEBIT')DEFAULT 'CASH'");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('running_services', function (Blueprint $table) {
			$table->dropColumn('beautician_id');
		});

		Schema::table('invoice_details', function (Blueprint $table) {
			$table->dropColumn('beautician_id');
		});

		Schema::table('invoice', function (Blueprint $table) {
			$table->enum('payment_type', ['CASH', 'ONLINE', 'CARD'])->default('CASH');
		});
	}
}
