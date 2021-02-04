<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariantExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variant_extras', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_varient_id')->unsigned();
            $table->integer('select_count')->nullable();
            $table->timestamps();
            $table->foreign('product_varient_id')->references('id')->on('product_varients')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variant_extras');
    }
}
