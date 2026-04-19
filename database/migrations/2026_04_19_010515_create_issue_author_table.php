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
    Schema::create('issue_author', function (Blueprint $table) {
        $table->foreignId('issue_id')->constrained()->onDelete('cascade');
        $table->foreignId('author_id')->constrained()->onDelete('cascade');
        $table->primary(['issue_id', 'author_id']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('issue_author');
    }
};
