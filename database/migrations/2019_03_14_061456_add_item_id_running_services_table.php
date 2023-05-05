<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddItemIdRunningServicesTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('running_services', function (Blueprint $table) {
			$platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
			$platform->registerDoctrineTypeMapping('enum', 'string');
			$table->dropForeign(['service_id']);
			$table->dropColumn('service_id');
			$table->integer('item_id')->default(0)->after('client_id');
			$table->string('name')->nullable()->after('item_id');
			$table->float('price')->nullable()->after('beautician_id');
			$table->enum('item_type', ['SERVICE', 'PRODUCT', 'PACKAGE'])->default('SERVICE')->after('price');
		});

		// Schema::disableForeignKeyConstraints();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('running_services', function (Blueprint $table) {
			$table->integer('service_id')->unsigned();
			$table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
			$table->dropColumn('item_id');
			$table->dropColumn('item_type');
		});
	}
}
