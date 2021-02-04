<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_variant_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('option_value_id')->unsigned();
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('product_varients')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_varients')->onDelete('cascade');
            $table->foreign('option_value_id')->references('id')->on('option_values')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variant_values');
    }
}
