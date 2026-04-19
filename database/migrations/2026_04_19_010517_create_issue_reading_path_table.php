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
    Schema::create('issue_reading_path', function (Blueprint $table) {
        $table->foreignId('issue_id')->constrained()->onDelete('cascade');
        $table->foreignId('reading_path_id')->constrained()->onDelete('cascade');
        $table->integer('order_position')->default(0);
        $table->boolean('start_here')->default(false);
        $table->primary(['issue_id', 'reading_path_id']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_reading_path');
    }
};
