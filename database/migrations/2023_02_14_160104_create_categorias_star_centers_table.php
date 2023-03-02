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
        Schema::create('categorias_star_centers', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('title');
            $table->string('type')->nullable(true);
            $table->string('image')->nullable(true);
            $table->integer('id_woo')->nullable(true);
            $table->integer('id_center');
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
        Schema::dropIfExists('categorias_star_centers');
    }
};
