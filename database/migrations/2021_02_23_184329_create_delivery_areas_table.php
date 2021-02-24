<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryAreasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('delivery_class_id')->unsigned()->nullable();
            $table->text('description')->nullable();
            $table->string('status')->default('available');
            $table->integer('longitude');
            $table->integer('latitude');
            $table->double('radius');
            $table->foreign('delivery_class_id')->references('id')->on('delivery_classes')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_areas');
    }
}
