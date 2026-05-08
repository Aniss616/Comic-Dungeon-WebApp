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
    Schema::create('volumes', function (Blueprint $table) {
        $table->id();
        $table->integer('comic_vine_id')->unique();
        $table->string('name');
        $table->longtext('description')->nullable();
        $table->text('cover_image')->nullable();
        $table->text('site_detail_url')->nullable();
        $table->integer('count_of_issues')->nullable();
        $table->json('first_issue')->nullable();
        $table->json('last_issue')->nullable();
        $table->foreignId('publisher_id')
            ->nullable()
            ->constrained('publishers')
            ->nullOnDelete();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('volumes');
    }
};
