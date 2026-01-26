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
        Schema::table('orders', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['user_id']);

            // Modify the column to be nullable
            $table->foreignId('user_id')->nullable()->change();

            // Re-add the foreign key with set null on delete
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop the foreign key
            $table->dropForeign(['user_id']);

            // This will fail if there are orders with null user_id
            $table->foreignId('user_id')->nullable(false)->change();

            // Re-add the foreign key with restrict on delete
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });
    }
};
