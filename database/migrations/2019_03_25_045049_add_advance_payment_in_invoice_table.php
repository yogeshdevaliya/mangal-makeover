<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvancePaymentInInvoiceTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('invoice', function (Blueprint $table) {
			DB::statement("ALTER TABLE invoice CHANGE COLUMN payment_type payment_type ENUM('CASH', 'ONLINE', 'CARD', 'DEBIT', 'ADVANCE_PAYMENT')DEFAULT 'CASH'");
			$table->float('advance_payment')->default(0)->after('grand_total');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('invoice', function (Blueprint $table) {
			DB::statement("ALTER TABLE invoice CHANGE COLUMN payment_type payment_type ENUM('CASH', 'ONLINE', 'CARD', 'DEBIT')DEFAULT 'CASH'");
			$table->dropColumn('advance_payment');
		});
	}
}
