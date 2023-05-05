<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDebitAmountAndAmountPaidInInvoiceTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('invoice', function (Blueprint $table) {
			$table->string('debit_amount')->nullable()->after('grand_total');
			$table->string('amount_paid')->nullable()->after('debit_amount');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('invoice', function (Blueprint $table) {
			$table->dropColumn('debit_amount');
			$table->dropColumn('amount_paid');
		});
	}
}
