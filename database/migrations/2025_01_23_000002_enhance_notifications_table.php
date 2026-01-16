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
        // Check if notifications table exists, if not create it
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('type');
                $table->morphs('notifiable');
                $table->text('data');
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // Add additional columns for enhanced functionality
        Schema::table('notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('notifications', 'notification_type')) {
                $table->string('notification_type')->nullable()->after('type');
            }
            if (!Schema::hasColumn('notifications', 'priority')) {
                $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal')->after('notification_type');
            }
            if (!Schema::hasColumn('notifications', 'channels_sent')) {
                $table->json('channels_sent')->nullable()->after('priority');
            }
            if (!Schema::hasColumn('notifications', 'firebase_response')) {
                $table->json('firebase_response')->nullable()->after('channels_sent');
            }
            if (!Schema::hasColumn('notifications', 'failed_channels')) {
                $table->json('failed_channels')->nullable()->after('firebase_response');
            }
            if (!Schema::hasColumn('notifications', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('failed_channels');
            }
            if (!Schema::hasColumn('notifications', 'sent_at')) {
                $table->timestamp('sent_at')->nullable()->after('scheduled_at');
            }
        });

        // Add indexes for better performance
        Schema::table('notifications', function (Blueprint $table) {
            $table->index(['notifiable_type', 'notifiable_id', 'read_at']);
            $table->index(['notification_type', 'created_at']);
            $table->index(['priority', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id', 'read_at']);
            $table->dropIndex(['notification_type', 'created_at']);
            $table->dropIndex(['priority', 'scheduled_at']);
            
            $table->dropColumn([
                'notification_type',
                'priority',
                'channels_sent',
                'firebase_response',
                'failed_channels',
                'scheduled_at',
                'sent_at'
            ]);
        });
    }
};
