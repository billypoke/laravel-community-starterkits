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
        Schema::table('starterkits', function (Blueprint $table) {
            $table->index(['created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::table('starterkit_tag', function (Blueprint $table) {
            $table->index(['tag_id', 'starterkit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('starterkit_related_tables', function (Blueprint $table) {
            //
        });
    }
};
