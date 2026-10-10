<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reporter_bills')) {
            Schema::create('reporter_bills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('portal_id')->constrained()->cascadeOnDelete();
                $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
                $table->string('billing_month', 7); // e.g. "2026-10"
                $table->integer('total_articles')->default(0);
                $table->decimal('rate_per_article', 10, 2)->default(500.00);
                $table->decimal('bonus_amount', 10, 2)->default(0.00);
                $table->decimal('deduction_amount', 10, 2)->default(0.00);
                $table->decimal('total_payable', 10, 2)->default(0.00);
                $table->string('payment_method')->default('bkash'); // bkash, nagad, bank, cash
                $table->string('account_number')->nullable();
                $table->string('transaction_id')->nullable();
                $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
                $table->timestamp('paid_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['portal_id', 'billing_month']);
                $table->index(['reporter_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reporter_bills');
    }
};
