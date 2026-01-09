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
        Schema::table('email_logs', function (Blueprint $table) {
            $table->string('cc')->nullable()->after('destinataire');
            $table->string('bcc')->nullable()->after('cc');
            $table->text('body_preview')->nullable()->after('sujet');
            
            // Add indexes for better query performance
            $table->index('destinataire');
            $table->index('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropIndex(['destinataire']);
            $table->dropIndex(['sent_at']);
            $table->dropColumn(['cc', 'bcc', 'body_preview']);
        });
    }
};
