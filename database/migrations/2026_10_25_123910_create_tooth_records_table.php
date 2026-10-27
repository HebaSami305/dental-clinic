<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
{
    Schema::create('tooth_records', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
        $table->unsignedTinyInteger('tooth_number');
        $table->string('condition')->default('healthy');
        $table->text('notes')->nullable();
        $table->timestamps();

        $table->unique(['patient_id', 'tooth_number']);
    });
}
    public function down(): void
    {
        Schema::dropIfExists('tooth_records');
    }
};
