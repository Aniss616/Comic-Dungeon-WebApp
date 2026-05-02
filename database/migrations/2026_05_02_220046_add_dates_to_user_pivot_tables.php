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
        Schema::table('user_reads', function (Blueprint $table) {
            $table->date('read_date')->nullable();
        });

        Schema::table('user_favourites', function (Blueprint $table) {
        $table->date('favourite_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_pivot_tables', function (Blueprint $table) {
            //
        });
    }
};
