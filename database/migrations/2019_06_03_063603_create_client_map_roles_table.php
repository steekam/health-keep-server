<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientMapRolesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('client_map_roles', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->unsignedBigInteger('client_id')->unsigned();
      $table->unsignedBigInteger('client_role_id')->unsigned();

      $table->foreign('client_id')
        ->references('client_id')
        ->on('clients')
        ->onDelete('cascade')
        ->onUpdate('cascade');
      $table->foreign('client_role_id')
        ->references('client_role_id')
        ->on('client_roles')
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
    Schema::dropIfExists('client_map_roles');
  }
}
