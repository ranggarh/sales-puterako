<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJasasTable extends Migration
{
    public function up()
    {
        Schema::create('jasas', function (Blueprint $table) {
            $table->bigIncrements('id_jasa');
            $table->unsignedBigInteger('id_penawaran');
            $table->decimal('profit_percent', 8, 2)->default(0);
            $table->decimal('profit_value', 15, 2)->default(0);
            $table->decimal('pph_percent', 8, 2)->default(0);
            $table->decimal('pph_value', 15, 2)->default(0);
            $table->decimal('bpjsk_percent', 8, 2)->nullable()->default(0); 
            $table->decimal('bpjsk_value', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_penawaran')->references('id_penawaran')->on('penawarans')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jasas');
    }
}