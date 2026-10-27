<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
{
    Schema::create('visits', function (Blueprint $table) {
        $table->id();
        $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
        $table->date('visited_at')->index();
        $table->text('diagnosis')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
