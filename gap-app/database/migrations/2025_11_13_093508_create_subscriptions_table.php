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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('status')->default('pending'); // e.g., pending, cancelled, active, expired
            $table->string('payment_method')->nullable();
            $table->decimal('amount_paid', 8, 2)->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('renewal_reminder')->default('enabled'); // e.g., enabled, disabled
            $table->string('duration_unit')->nullable(); // e.g., days, months, years, hours
            $table->string('duration')->nullable(); // e.g., '30', '6'.
            $table->longText('subscription_proof')->nullable(); // path to uploaded proof

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
