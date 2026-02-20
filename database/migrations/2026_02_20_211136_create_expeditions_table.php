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
        Schema::create('expeditions', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('protocol')->unique();

            $table->foreignId('kingdom_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->text('journey_description');

            $table->string('status');

            $table->text('rejection_reason')->nullable();

            $table->foreignId('decision_by')
                ->nullable()
                ->constrained('council_members')
                ->nullOnDelete();

            $table->timestamp('decided_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expeditions');
    }
};
