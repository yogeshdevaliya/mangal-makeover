<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ServiceAsTextInPackageTable extends Migration {
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table('package', function (Blueprint $table) {
			$platform = Schema::getConnection()->getDoctrineSchemaManager()->getDatabasePlatform();
			$platform->registerDoctrineTypeMapping('enum', 'string');
			$table->dropForeign(['service_id']);
			$table->dropColumn('service_id');
			$table->string('service')->after('slug')->nullable();
		});

		Schema::disableForeignKeyConstraints();
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table('package', function (Blueprint $table) {
			$table->integer('service_id')->unsigned();
			$table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
		});
	}
}
