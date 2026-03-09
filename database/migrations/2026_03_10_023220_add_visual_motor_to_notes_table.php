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
        Schema::table('notes', function (Blueprint $table) {
            $table->boolean('ei_visual_motor')
                ->nullable()
                ->after('ei_visual_closure');

            $table->boolean('ef_visual_motor')
                ->nullable()
                ->after('ef_visual_closure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn([
                'ei_visual_motor',
                'ef_visual_motor',
            ]);
        });
    }
};
