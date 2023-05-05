<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnCreditDebitStatusInInvoiceTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('invoice', function (Blueprint $table) {
			DB::statement("ALTER TABLE invoice CHANGE COLUMN payment_type payment_type ENUM('CASH', 'ONLINE', 'CARD', 'DEBIT', 'ADVANCE_PAYMENT','ON_CREDIT','ON_CREDIT+CASH','ON_CREDIT+DEBIT')DEFAULT 'CASH'");
			$table->float('on_credit_debit')->default(0)->after('on_credit_cash');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('invoice', function (Blueprint $table) {
			DB::statement("ALTER TABLE invoice CHANGE COLUMN payment_type payment_type ENUM('CASH', 'ONLINE', 'CARD', 'DEBIT', 'ADVANCE_PAYMENT','ON_CREDIT','ON_CREDIT+CASH')DEFAULT 'CASH'");
			$table->dropColumn('on_credit_debit');
		});
	}
}
