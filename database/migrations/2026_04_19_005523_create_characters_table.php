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
    Schema::create('characters', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('alias')->nullable();
        $table->text('abilities')->nullable();
        $table->string('avatar_url')->nullable();
        $table->string('universe')->nullable();
        $table->integer('comic_vine_id')->nullable()->unique();
        $table->text('deck')->nullable(); // Comic Vine's short description field
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
