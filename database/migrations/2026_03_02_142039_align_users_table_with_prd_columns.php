<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('address')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('gender')->nullable();
            $table->string('status')->default('active');
        });

        DB::table('users')->whereNull('first_name')->update([
            'first_name' => DB::raw('name'),
        ]);

        DB::table('users')->whereNull('last_name')->update([
            'last_name' => '',
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable();
        });

        DB::table('users')->whereNull('name')->update([
            'name' => DB::raw('first_name'),
        ]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('middle_name');
            $table->dropColumn('last_name');
            $table->dropColumn('address');
            $table->dropColumn('contact_no');
            $table->dropColumn('gender');
            $table->dropColumn('status');
        });
    }
};
