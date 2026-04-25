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
        Schema::create('issues', function (Blueprint $table) {
            $table->id();
            $table->integer('comic_vine_id')->unique();
            $table->string('name');
            $table->integer('issue_number')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->date('cover_date')->nullable();
            $table->foreignId('volume_id')
                ->constrained('volumes')
                ->cascadeOnDelete();

            $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issues');
    }
};
