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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('author_id')->nullable();
            $table->string('author_type')->nullable();
            $table->unsignedBigInteger('folder_id')->nullable()->index();
            $table->string('name')->unique();
            $table->string('slug')->unique()->index();
            $table->text('path')->nullable();
            $table->text('file')->nullable();
            $table->json('technical_information')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('file_type')->nullable();
            $table->boolean('is_private')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
};
