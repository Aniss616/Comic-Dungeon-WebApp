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
    Schema::create('characters', function (Blueprint $table) {
        $table->id();
        $table->integer('comic_vine_id')->unique();
        $table->string('name');
        $table->string('real_name')->nullable();
        $table->text('description')->nullable();
        $table->json('aliases')->nullable();
        $table->string('image')->nullable();
        $table->string('birth')->nullable();
        $table->tinyInteger('gender')->nullable();
        $table->string('origin')->nullable();
        $table->string('publisher')->nullable();
        $table->json('powers')->nullable();
        $table->json('teams')->nullable();
        $table->json('character_friends')->nullable();
        $table->json('character_enemies')->nullable();
        $table->json('first_appeared_in_issue')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
