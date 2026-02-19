<?php
// app/Http/Controllers/ZkAttendanceController.php

namespace App\Http\Controllers;

use Jmrashed\Zkteco\Lib\ZKTeco;
use App\Services\ZkService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Device;
use App\Models\ZkUser;
use Illuminate\Support\Facades\Log;

class ZkAttendanceController extends Controller
{

    protected ZKTeco $zk;
    protected string $ip;
    protected int $port;

    public function __construct(ZkService $zkService)
    {
        $this->zkService = $zkService;
        $this->ip = env('ZK_DEVICE_IP', '192.168.1.215');
        $this->port = env('ZK_DEVICE_PORT', 4370);
        $this->zk = new ZKTeco($this->ip, $this->port);
    }
    /**
     * Display the attendance page
     */
    public function index()
    {
        try {
            $data = $this->zkService->getDeviceData();
                return view('zk.attendance.index', $data);
            } catch (\Exception $e) {
                Log::error('Attendance error: ' . $e->getMessage());
                return view('zk.attendance.index', [
                    'sample_attendance' => [],
                    'error' => $e->getMessage()
                ]);
            }
    }

    /**
     * Sync attendance from device
     */
    public function sync(Request $request)
    {
        // Add your ZK device sync logic here
        return response()->json(['message' => 'Sync started successfully']);
    }

    /**
     * Export attendance data
     */
    public function export(Request $request)
    {
        // Add export logic (CSV, Excel, PDF)
        return back()->with('success', 'Export completed');
    }
}