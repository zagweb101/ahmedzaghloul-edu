<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_orders', function (Blueprint $table) {
            $table->string('gateway_charge_id')->nullable()->after('payment_driver');
            $table->string('checkout_url', 2048)->nullable()->after('gateway_charge_id');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_orders', function (Blueprint $table) {
            $table->dropColumn(['gateway_charge_id', 'checkout_url']);
        });
    }
};
