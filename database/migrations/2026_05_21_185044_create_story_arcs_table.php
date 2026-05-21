<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('story_arcs', function (Blueprint $table) {
            $table->id();
            $table->integer('comic_vine_id')->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('story_arcs');
    }
};