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
            $table->bigInteger('product_varient_id')->unsigned();
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('option_values_id')->unsigned();
            $table->timestamps();
            $table->unique(['product_varient_id', 'product_id', 'option_values_id'], 'product_variant_option_value');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_varient_id')->references('id')->on('product_varients')->onDelete('cascade');
            $table->foreign('option_values_id')->references('id')->on('option_values')->onDelete('cascade');
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
