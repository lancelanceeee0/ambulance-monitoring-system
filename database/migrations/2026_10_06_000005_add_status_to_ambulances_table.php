<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ambulances', 'status')) {
            Schema::table('ambulances', function (Blueprint $table) {
                $table->string('status')->default('back_to_base')->after('photo_path');
            });
        }

        // Backfill: vehicles whose latest dispatch is still On Mission stay on_mission;
        // everything else (including no history) is back_to_base.
        $latestIds = DB::table('dispatch_logs')->selectRaw('MAX(id) as id')->groupBy('plate_number');
        $onMission = DB::table('dispatch_logs')
            ->whereIn('id', $latestIds)
            ->where('status', 'On Mission')
            ->pluck('plate_number');

        DB::table('ambulances')->update(['status' => 'back_to_base']);
        if ($onMission->isNotEmpty()) {
            DB::table('ambulances')->whereIn('plate_number', $onMission)->update(['status' => 'on_mission']);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('ambulances', 'status')) {
            Schema::table('ambulances', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
