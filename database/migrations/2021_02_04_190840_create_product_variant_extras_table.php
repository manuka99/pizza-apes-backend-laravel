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
            $table->bigInteger('product_varients_id')->unsigned();
            $table->bigInteger('extras_id')->unsigned();
            $table->integer('select_count')->nullable();
            $table->string('display_name')->nullable();
            $table->timestamps();
            $table->foreign('product_varients_id')->references('id')->on('product_varients')->onDelete('cascade');
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
        Schema::dropIfExists('product_variant_extras');
    }
}
