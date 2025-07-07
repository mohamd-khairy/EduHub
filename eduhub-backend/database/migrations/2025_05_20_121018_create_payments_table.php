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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->decimal('amount', 8, 2);
            $table->date('payment_date');
            $table->enum('method', ['كاش', 'تحويل بنكي', 'فيزا']);
            $table->enum('status', ['paid', 'pending', 'cancelled'])->default('paid');
            $table->text('note')->nullable();
            $table->foreignId('study_year_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
