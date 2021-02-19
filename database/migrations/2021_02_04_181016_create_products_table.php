<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('url_name');
            $table->text('product_name')->nullable();
            $table->double('minimun_price')->nullable();
            $table->double('maximum_price')->nullable();
            $table->string('type')->default('simple');
            $table->string('status')->default('draft');
            $table->string('visibility')->default('public');
            $table->timestamp('published_on')->current();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_trashed')->default(false);
            $table->integer('default_variation')->nullable();
            $table->string('image')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->string('label')->nullable();
            $table->string('symbol')->nullable()->default('meat');
            $table->timestamps();
            $table->unique('url_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
