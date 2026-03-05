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
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('sessions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('content')->nullable();
            $table->boolean('bo_cooperative')->nullable();
            $table->boolean('bo_calm_regulated')->nullable();
            $table->boolean('bo_restless_fidgety')->nullable();
            $table->boolean('bo_easily_frustrated')->nullable();
            $table->boolean('bo_tantrums')->nullable();
            $table->boolean('bo_meltdowns')->nullable();
            $table->boolean('bo_avoidant')->nullable();
            $table->boolean('bo_aggressive')->nullable();
            $table->boolean('bo_other')->nullable();
            $table->text('bo_other_details')->nullable();
            $table->text('am_activities_and_management')->nullable();
            $table->timestamps();

            $table->unique('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
