<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataDoctorsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('data_doctors', function (Blueprint $table) {
      $table->bigIncrements('doctor_id');
      $table->unsignedBigInteger('client_id')->unsigned();
      $table->string('registration_number')->nullable();
      $table->string('valid_license_until')->nullable();
      $table->boolean('suspended')->default(true);
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
    Schema::dropIfExists('data_doctors');
  }
}
