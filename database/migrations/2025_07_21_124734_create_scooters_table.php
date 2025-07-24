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
        Schema::create('scooters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('max_speed')->comment('Maximum speed of the scooter in km/h');
            $table->string('range')->comment('Maximum distance the scooter can travel on a full charge');
            $table->json('features')->comment('Features of the scooter, such as GPS, Bluetooth, etc.');
            $table->json('description')->comment('Description of the scooter');
            $table->json('pricing')->comment('Pricing details for the scooter');
            $table->integer('quantity')->comment('Quantity of the scooter');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scooters');
    }
};
