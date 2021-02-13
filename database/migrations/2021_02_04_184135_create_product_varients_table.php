<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVarientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_varients', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->string('sku_id')->nullable();
            $table->string('image')->nullable();
            $table->double('regular_price')->nullable();
            $table->double('offer_price')->nullable();
            $table->boolean('schedule_offer')->nullable()->default(false);;
            $table->timestamp('offer_from')->nullable();
            $table->timestamp('offer_to')->nullable();
            $table->boolean('manage_stock')->nullable()->default(false);
            $table->integer('stock_qty')->nullable();
            $table->integer('low_stock_threshold')->nullable();
            $table->boolean('back_orders')->nullable();
            $table->integer('order_limit_count')->nullable();
            $table->integer('order_limit_days')->nullable();
            $table->integer('length')->nullable();
            $table->integer('height')->nullable();
            $table->integer('width')->nullable();
            $table->integer('weight')->nullable();
            $table->bigInteger('shipping_class')->nullable();
            $table->timestamps();
            $table->unique(['product_id', 'sku_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_varients');
    }
}
