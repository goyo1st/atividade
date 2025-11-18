<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->constrained()->onDelete('restrict');
        $table->decimal('amount', 15, 2);
        $table->enum('direction', ['income','expense']);
        $table->text('description')->nullable();
        $table->date('happened_at');
        $table->boolean('is_recurring')->default(false);
        $table->foreignId('recurrence_id')->nullable()->constrained()->onDelete('set null');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
