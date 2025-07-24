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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('purpose'); // Purpose of the transaction (e.g., order payment, refund, etc.)
            $table->foreignId('order_id')->nullable()->constrained()->onDelete('cascade'); // Associated order ID
            $table->decimal('transaction_amount', 15, 2);
            $table->string('currency')->default('usd'); // Default currency
            $table->string('provider')->default('stripe'); // Payment provider, default to Stripe
            $table->string('stripe_session_id')->nullable(); // Stripe session ID for payment
            $table->enum('status', ['pending', 'successful', 'failed'])->default('pending'); // Transaction status
            $table->text('description')->nullable(); // Description of the transaction
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
