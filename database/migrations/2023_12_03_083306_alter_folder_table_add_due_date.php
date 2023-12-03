<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dateTime('due_date')->nullable();;
        });
    }


    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropColumn('due_date');
        });
    }
};
