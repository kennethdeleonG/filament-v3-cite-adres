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
        Schema::create('folders', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->unsignedInteger('author_id')->nullable();
            $table->string('author_type')->nullable();
            $table->unsignedInteger('folder_id')->nullable();
            $table->unsignedInteger('_lft');
            $table->unsignedInteger('_rgt');
            $table->string('name');
            $table->boolean('is_private')->default(true);
            $table->string('slug');
            $table->text('path');
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
        Schema::dropIfExists('folders');
    }
};
