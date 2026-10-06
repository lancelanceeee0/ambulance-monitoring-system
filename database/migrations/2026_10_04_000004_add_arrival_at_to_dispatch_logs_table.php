<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // This migration is intentionally a no-op when the field already exists.
        // It also backfills older records if this project already had dispatch logs.
        if (!Schema::hasColumn('dispatch_logs', 'arrival_at')) {
            Schema::table('dispatch_logs', function (Blueprint $table) {
                $table->dateTime('arrival_at')->nullable()->after('time');
            });
        }

        DB::table('dispatch_logs')
            ->where('status', 'On Mission')
            ->whereNull('arrival_at')
            ->orderBy('id')
            ->get(['id', 'date', 'time'])
            ->each(function ($log) {
                $arrivalAt = Carbon::parse($log->date . ' ' . $log->time)->addMinutes(5);
                DB::table('dispatch_logs')
                    ->where('id', $log->id)
                    ->update(['arrival_at' => $arrivalAt]);
            });
    }

    public function down(): void
    {
        if (Schema::hasColumn('dispatch_logs', 'arrival_at')) {
            Schema::table('dispatch_logs', function (Blueprint $table) {
                $table->dropColumn('arrival_at');
            });
        }
    }
};
