<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExtrasValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extras_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('extras_id')->unsigned();
            $table->string('name');
            $table->string('image')->nullable();
            $table->double('price')->nullable();
            $table->string('layer_image')->nullable();
            $table->timestamps();
            $table->foreign('extras_id')->references('id')->on('extras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('extras_values');
    }
}
