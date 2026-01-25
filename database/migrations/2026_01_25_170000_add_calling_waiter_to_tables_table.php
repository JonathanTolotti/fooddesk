<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->boolean('calling_waiter')->default(false)->after('is_active');
            $table->timestamp('called_waiter_at')->nullable()->after('calling_waiter');
        });
    }

    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['calling_waiter', 'called_waiter_at']);
        });
    }
};
