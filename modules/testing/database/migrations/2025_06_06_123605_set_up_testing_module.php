<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class SetUpTestingModule extends Migration
{
  public function up()
  {
    // Schema::create('testing', function(Blueprint $table) {
    // 	$table->bigIncrements('id');
    // 	$table->timestamps();
    // 	$table->softDeletes();
    // });
  }

  public function down()
  {
    // Schema::dropIfExists('testing');
  }
}
