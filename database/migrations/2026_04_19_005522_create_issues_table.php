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
    Schema::create('issues', function (Blueprint $table) {
        $table->id();
        $table->foreignId('comic_id')->constrained()->onDelete('cascade');
        $table->integer('issue_number');
        $table->string('cover_image')->nullable();
        $table->date('release_date')->nullable();
        $table->boolean('recommended_start')->default(false);
        $table->integer('comic_vine_id')->nullable()->unique();
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
