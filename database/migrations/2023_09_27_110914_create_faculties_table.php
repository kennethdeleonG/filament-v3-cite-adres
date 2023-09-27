<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->longText('address')->nullable();
            $table->string('mobile')->nullable();
            $table->string('gender')->nullable();
            $table->longText('designation')->nullable();

            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('status')->default('active')->index()->nullable();

            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
