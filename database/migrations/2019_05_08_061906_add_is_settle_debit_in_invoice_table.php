<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsSettleDebitInInvoiceTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('invoice', function (Blueprint $table) {
			$table->float('settle_debit_amount')->default(0)->after('amount_paid');
			$table->integer('is_settle_debit')->default(0)->after('payment_history');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('invoice', function (Blueprint $table) {
			$table->dropColumn('settle_debit_amount');
			$table->dropColumn('is_settle_debit');
		});
	}
}
