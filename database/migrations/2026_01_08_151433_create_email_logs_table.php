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
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->string('destinataire');
            $table->string('cc')->nullable();
            $table->string('bcc')->nullable();
            $table->string('sujet');
            $table->text('body_preview')->nullable();
            $table->timestamp('sent_at');
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index('destinataire');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
