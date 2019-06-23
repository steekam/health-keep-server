<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
      $table->string('description', 255)->nullable();
      $table->date('appointment_date');
      $table->time('appointment_time');
      $table->string('location')->nullable();
      $table->unsignedBigInteger('client_id')->unsigned();
      $table->boolean('archived')->default(false);
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
