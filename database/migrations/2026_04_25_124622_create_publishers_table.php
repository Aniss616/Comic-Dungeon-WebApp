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
    Schema::create('publishers', function (Blueprint $table) {
        $table->id();
        $table->integer('comic_vine_id')->unique();
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('logo_url')->nullable();
        $table->string('location_city')->nullable();
        $table->string('location_state')->nullable();
        $table->string('location_country')->nullable();
        $table->json('aliases')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publishers');
    }
};
