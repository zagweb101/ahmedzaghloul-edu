<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->timestamp('expiring_notified_at')->nullable()->after('ends_at');
        });

        Schema::table('live_event_registrations', function (Blueprint $table) {
            $table->timestamp('started_notified_at')->nullable()->after('reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn('expiring_notified_at');
        });

        Schema::table('live_event_registrations', function (Blueprint $table) {
            $table->dropColumn('started_notified_at');
        });
    }
};
