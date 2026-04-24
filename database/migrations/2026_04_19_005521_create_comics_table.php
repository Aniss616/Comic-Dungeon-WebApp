<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('comics', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('genre')->nullable();
        $table->text('description')->nullable();
        $table->string('cover_image')->nullable();
        $table->integer('comic_vine_id')->nullable()->unique();
        $table->integer('start_year')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comics');
    }
};
