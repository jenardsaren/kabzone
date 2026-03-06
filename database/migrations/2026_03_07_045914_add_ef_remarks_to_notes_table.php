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
            $table->text('ef_sensory_remarks')->nullable()->after('ef_regulation_independent');
            $table->text('ef_fine_motor_remarks')->nullable()->after('ef_fine_motor_assistance_type_verbal');
            $table->text('ef_cognitive_remarks')->nullable()->after('ef_cognitive_assistance_type_verbal');
            $table->text('ef_visual_remarks')->nullable()->after('ef_visual_assistance_type_verbal');
            $table->text('ef_social_remarks')->nullable()->after('ef_social_assistance_type_verbal');
            $table->text('ef_executive_remarks')->nullable()->after('ef_executive_assistance_type_verbal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->dropColumn([
                'ef_sensory_remarks',
                'ef_fine_motor_remarks',
                'ef_cognitive_remarks',
                'ef_visual_remarks',
                'ef_social_remarks',
                'ef_executive_remarks',
            ]);
        });
    }
};
