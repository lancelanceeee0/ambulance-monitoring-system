<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('dispatch_logs', 'returned_at')) {
            Schema::table('dispatch_logs', function (Blueprint $table) {
                $table->dateTime('returned_at')->nullable();
            });
        }

        // Backfill: older rows already marked as returned get a return time.
        DB::table('dispatch_logs')
            ->where('status', '!=', 'On Mission')
            ->whereNull('returned_at')
            ->update(['returned_at' => DB::raw('COALESCE(arrival_at, updated_at)')]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('dispatch_logs', 'returned_at')) {
            Schema::table('dispatch_logs', function (Blueprint $table) {
                $table->dropColumn('returned_at');
            });
        }
    }
};
