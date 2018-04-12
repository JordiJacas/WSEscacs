<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreaPartidas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('partidas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_jugador_negro')->unsigned();
            $table->integer('id_jugador_blanco')->unsigned();
            $table->enum('turno', ['b', 'n'])->default("b");
            $table->timestamps();

            $table->index('id_jugador_negro');
            $table->index('id_jugador_blanco');
            $table->index('turno');
            
            $table->foreign('id_jugador_negro')->references('id')->on('users');
            $table->foreign('id_jugador_blanco')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partidas');
    }
}
