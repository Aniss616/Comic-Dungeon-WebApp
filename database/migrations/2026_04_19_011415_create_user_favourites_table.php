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
    Schema::create('user_favourites', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('issue_id')->constrained()->onDelete('cascade');
        $table->date('favourite_date')->nullable();
        $table->primary(['user_id', 'issue_id']);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_favourites');
    }
};
