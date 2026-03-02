<?php

use App\Enums\SessionStatus;
use App\Enums\SessionType;
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
        if (! Schema::hasTable('http_sessions') && $this->hasLegacyFrameworkSessionTable()) {
            Schema::rename('sessions', 'http_sessions');
        }

        if (! Schema::hasTable('http_sessions')) {
            Schema::create('http_sessions', function (Blueprint $table): void {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        if (Schema::hasTable('sessions')) {
            return;
        }

        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->time('time');
            $table->string('type')->default(SessionType::Regular->value);
            $table->foreignId('client_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('therapist_id')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('assistant_id')->nullable()->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('description')->nullable();
            $table->longText('notes')->nullable();
            $table->string('status')->default(SessionStatus::Pending->value);
            $table->timestamps();

            $table->index(['date', 'time']);
            $table->index('client_id');
            $table->index('therapist_id');
            $table->index('assistant_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
    }

    private function hasLegacyFrameworkSessionTable(): bool
    {
        if (! Schema::hasTable('sessions')) {
            return false;
        }

        return Schema::hasColumn('sessions', 'payload')
            && Schema::hasColumn('sessions', 'last_activity')
            && Schema::hasColumn('sessions', 'ip_address')
            && Schema::hasColumn('sessions', 'user_agent')
            && ! Schema::hasColumn('sessions', 'client_id')
            && ! Schema::hasColumn('sessions', 'therapist_id');
    }
};
