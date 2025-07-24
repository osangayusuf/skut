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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('scooter_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'paid', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total_price', 10, 2); // Total price of the order
            $table->timestamp('start_time'); // Start time of the order
            $table->timestamp('end_time')->nullable();
            $table->string('pickup_location'); // Location where the scooter is picked up
            $table->string('stripe_session_id')->nullable(); // Stripe session ID for payment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
