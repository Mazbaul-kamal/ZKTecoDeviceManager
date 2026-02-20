<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ZkService;
use App\Models\Attendance;

class SyncZkAttendance extends Command
{
    protected $signature = 'zk:sync';
    protected $description = 'Sync ZK device attendance to DB';

    protected $zkService;

    public function __construct(ZkService $zkService)
    {
        parent::__construct();
        $this->zkService = $zkService;
    }

public function handle()
{
    $devices = \App\Models\Device::all();

    foreach ($devices as $device) {
        try {
            $zkService = new ZkService($device->ip_address, $device->port);
            $attendances = $zkService->getAttendance();

            foreach ($attendances as $log) {
                Attendance::updateOrCreate(
                    ['uid' => $log['uid'], 'timestamp' => $log['timestamp'], 'device_id' => $device->id],
                    ['user_id' => $log['id'], 'state' => $log['state']]
                );
            }

            $device->update([
                'connection_status' => 'Connected',
                'last_seen' => now(),
            ]);

        } catch (\Exception $e) {
            $device->update(['connection_status' => 'Failed']);
            Log::error("Device {$device->name} sync failed: ".$e->getMessage());
        }
    }

    $this->info('All devices synced successfully.');
}
}
