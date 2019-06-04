<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('appointments', function (Blueprint $table) {
      $table->bigIncrements('appointment_id');
      $table->string('title');
      $table->string('description', 255);
      $table->dateTime('appointment_date');
      $table->string('location')->nullable();
      $table->unsignedBigInteger('client_id')->unsigned();
      $table->string('status');
      $table->timestamps();

      $table->foreign('client_id')
        ->references('client_id')
        ->on('clients')
        ->onDelete('cascade')
        ->onUpdate('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('appointments');
  }
}
