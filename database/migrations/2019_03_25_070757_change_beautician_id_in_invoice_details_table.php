<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeBeauticianIdInInvoiceDetailsTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('invoice_details', function (Blueprint $table) {
			$platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
			$platform->registerDoctrineTypeMapping('enum', 'string');
			$table->dropForeign(['beautician_id']);
			$table->dropColumn('beautician_id');
		});

		Schema::disableForeignKeyConstraints();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('invoice_details', function (Blueprint $table) {
			$table->integer('beautician_id')->unsigned()->nullable()->after('name');
			$table->foreign('beautician_id')->references('id')->on('employees')->onDelete('cascade');
		});
	}
}
