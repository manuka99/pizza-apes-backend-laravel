<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuggestedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suggested_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pid_parent')->unsigned()->nullable();
            $table->bigInteger('pid')->unsigned()->nullable();
            $table->string('type')->nullable();
            $table->timestamps();
            $table->unique(['pid', 'pid_parent']);
            $table->foreign('pid')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('pid_parent')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('suggested_products');
    }
}
