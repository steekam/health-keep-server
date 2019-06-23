<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRemindersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reminders', function (Blueprint $table) {
			$table->bigIncrements('reminder_id');
			$table->date('start_date');
			$table->time('reminder_time');
			$table->boolean('repeat')->default(false);
			$table->string('frequency')->nullable();
			$table->unsignedBigInteger('reminderble_id');
			$table->string('reminderble_type');
			$table->boolean('active')->default(true);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('reminders');
	}
}
