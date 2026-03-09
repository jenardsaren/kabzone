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
            $table->boolean('ei_gross_motor_assistance_backward_chaining')->nullable()->after('ei_gross_motor_assistance_cues');
            $table->boolean('ei_fine_motor_assistance_backward_chaining')->nullable()->after('ei_fine_motor_assistance_cues');
            $table->boolean('ei_cognitive_assistance_backward_chaining')->nullable()->after('ei_cognitive_assistance_cues');
            $table->boolean('ei_visual_assistance_backward_chaining')->nullable()->after('ei_visual_assistance_cues');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn([
                'ei_gross_motor_assistance_backward_chaining',
                'ei_fine_motor_assistance_backward_chaining',
                'ei_cognitive_assistance_backward_chaining',
                'ei_visual_assistance_backward_chaining',
            ]);
        });
    }
};
