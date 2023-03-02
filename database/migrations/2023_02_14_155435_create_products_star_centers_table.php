<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products_star_centers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->string('type');
            $table->string('images');
            $table->string('sku');
            $table->decimal('price_original');
            $table->integer('stick');
            $table->decimal('price');
            $table->boolean('sync');
            $table->string('body');
            $table->integer('id_woo');
            $table->integer('id_categoria');
            $table->boolean('comprobacion');
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
        Schema::dropIfExists('products_star_centers');
    }
};
