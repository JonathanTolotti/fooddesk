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
        Schema::create('table_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->constrained('tables')->onDelete('cascade');
            $table->string('event', 20);
            $table->string('field', 50)->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('table_histories');
    }
};
