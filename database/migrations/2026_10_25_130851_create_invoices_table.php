<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
        $table->foreignId('visit_id')->nullable()->unique()->constrained()->nullOnDelete();
        $table->string('number')->nullable()->unique();
        $table->date('issued_at');
        $table->decimal('subtotal', 10, 2);
        $table->decimal('discount', 10, 2)->default(0);
        $table->decimal('total', 10, 2);
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
