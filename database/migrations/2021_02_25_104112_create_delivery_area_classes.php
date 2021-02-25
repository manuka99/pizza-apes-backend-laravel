<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryAreaClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_classes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('delivery_class_id')->unsigned();
            $table->bigInteger('delivery_area_id')->unsigned();
            $table->timestamps();
            $table->foreign('delivery_class_id')->references('id')->on('delivery_classes')->onDelete('cascade');
            $table->foreign('delivery_area_id')->references('id')->on('delivery_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area_classes');
    }
}
