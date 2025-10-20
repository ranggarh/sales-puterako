<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenawaranDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('penawaran_details', function (Blueprint $table) {
            $table->id('id_penawaran_detail');
            $table->unsignedBigInteger('id_penawaran');
            $table->string('area')->nullable();
            $table->string('nama_section')->nullable();
            $table->string('no')->nullable();
            $table->string('tipe')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('qty')->nullable();
            $table->string('satuan')->nullable();
            $table->double('harga_satuan')->nullable();
            $table->double('harga_total')->nullable();
            $table->double('hpp')->nullable();
            $table->double('profit')->nullable();
            $table->timestamps();

            $table->foreign('id_penawaran')->references('id_penawaran')->on('penawarans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('penawaran_details');
    }
}