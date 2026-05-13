<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'paypal_transaction_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('paypal_transaction_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'paypal_transaction_id')) {
            return;
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('paypal_transaction_id')->nullable()->after('stripe_payment_intent_id');
        });
    }
};
