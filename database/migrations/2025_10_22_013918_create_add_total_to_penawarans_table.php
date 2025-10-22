<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('penawarans', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->nullable()->after('tiket');
            $table->decimal('ppn_persen', 5, 2)->default(11)->after('total');
            $table->decimal('ppn_nominal', 15, 2)->nullable()->after('ppn_persen');
            $table->decimal('grand_total', 15, 2)->nullable()->after('ppn_nominal');
        });
    }

    public function down()
    {
        Schema::table('penawarans', function (Blueprint $table) {
            $table->dropColumn(['total', 'ppn_persen', 'ppn_nominal', 'grand_total']);
        });
    }
};