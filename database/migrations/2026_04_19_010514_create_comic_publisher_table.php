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
    Schema::create('comic_publisher', function (Blueprint $table) {
        $table->foreignId('comic_id')->constrained()->onDelete('cascade');
        $table->foreignId('publisher_id')->constrained()->onDelete('cascade');
        $table->primary(['comic_id', 'publisher_id']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comic_publisher');
    }
};
