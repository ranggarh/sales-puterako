<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJasaDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('jasa_details', function (Blueprint $table) {
            $table->id('id_jasa_detail');
            $table->unsignedBigInteger('id_jasa');
            $table->unsignedBigInteger('id_penawaran');
            $table->string('nama_section')->nullable();
            $table->string('no')->nullable();
            $table->string('deskripsi')->nullable();
            $table->integer('vol')->nullable();
            $table->integer('hari')->nullable();
            $table->integer('orang')->nullable();
            $table->string('unit')->nullable();
            $table->double('total')->nullable();
            $table->double('profit')->nullable();
            $table->double('pph')->nullable();
            $table->timestamps();

            $table->foreign('id_penawaran')->references('id_penawaran')->on('penawarans')->onDelete('cascade');
            $table->foreign('id_jasa')->references('id_jasa')->on('jasas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jasa_details');
    }
}