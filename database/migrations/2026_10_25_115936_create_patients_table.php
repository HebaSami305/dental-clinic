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
    Schema::create('patients', function (Blueprint $table) {
        $table->id();
        $table->string('file_number')->nullable()->unique();
        $table->string('name');
        $table->string('phone', 20)->index();
        $table->enum('gender', ['male', 'female']);
        $table->date('birth_date')->nullable();
        $table->string('address')->nullable();
        $table->text('allergies')->nullable();
        $table->text('medical_history')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
