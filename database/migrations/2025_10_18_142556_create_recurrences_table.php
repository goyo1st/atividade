<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurrences', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Ex: "Mensal", "Semanal", etc
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurrences');
    }
};
