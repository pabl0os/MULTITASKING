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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('max_in_process_tasks')->default(8);
            $table->integer('notify_sameday_hours')->default(2);
            $table->integer('notify_diffday_days')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['max_in_process_tasks', 'notify_sameday_hours', 'notify_diffday_days']);
        });
    }
};
