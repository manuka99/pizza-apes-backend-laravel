<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryClassOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_class_options', function (Blueprint $table) {
            $table->id();
            $table->double('quantity');
            $table->double('displacement');
            $table->bigInteger('delivery_class_id')->unsigned();
            $table->timestamps();
            $table->foreign('delivery_class_id')->references('id')->on('delivery_classes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_class_options');
    }
}
