<?php

namespace App\Http\Controllers;

// use Rats\Zkteco\Lib\ZKTeco;
use Jmrashed\Zkteco\Lib\ZKTeco;
use App\Services\ZkService;
use Illuminate\Http\ZKBioService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Device;
use App\Models\ZkUser;
use Illuminate\Support\Facades\Log;
use App\Models\Attendance;

class ZKDashboardController extends Controller
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
     * Display the dashboard
     */
    // public function dashboard()
    // {
    //    $attendances = $this->zkService->getAttendance();
    //     // 
    //     try {
    //         $data = $this->zkService->getDeviceData();
    //         $data['attendances'] = $attendances;
    //         return view('dashboard', $data);
    //     } catch (\Exception $e) {
    //         Log::error('Dashboard error: ' . $e->getMessage());
    //         return view('dashboard', [
    //             'connection' => 'Failed',
    //             'device_info' => [],
    //             'users_count' => 0,
    //             'attendance_count' => 0,
    //             'sample_users' => [],
    //             'attendances' => [],
    //             'error' => $e->getMessage()
    //         ]);
    //     }
    // }

    public function dashboard()
{
    try {

        $attendances = Attendance::latest('timestamp')->limit(10)->get();
        $users_count = \App\Models\User::count();
        $attendance_count = Attendance::count();
        $attendances = $this->zkService->getAttendance();
        $employees = $this->zkService->getEmployees();
        
        $data = $this->zkService->getDeviceData();
        $data['attendances'] = $attendances;

        return view('dashboard', $data);

    } catch (\Exception $e) {
        Log::error('Dashboard error: ' . $e->getMessage());

        return view('dashboard', [
            'connection' => 'Failed',
            'device_info' => [],
            'users_count' => 0,
            'attendance_count' => 0,
            'sample_users' => [],
            'attendances' => [],
            'employees' => [],
            'error' => $e->getMessage()
        ]);
    }
}



// public function dashboard()
// {
//     try {
//         $devices = \App\Models\Device::all();
//         $attendances = Attendance::latest('timestamp')->limit(10)->get();
//         $users_count = \App\Models\User::count();
//         $attendance_count = Attendance::count();

//         return view('dashboard', compact('devices','attendances','users_count','attendance_count'));

//     } catch (\Exception $e) {
//         Log::error('Dashboard error: ' . $e->getMessage());
//         return view('dashboard', [
//             'devices' => [],
//             'attendances' => [],
//             'users_count' => 0,
//             'attendance_count' => 0,
//             'error' => $e->getMessage()
//         ]);
//     }
// }

/**post time to device */
    public function setTime()
    {
        try {
            $this->zkService->syncTime();
            return back()->with('success', 'Device time synchronized successfully.');
        } catch (\Exception $e) {
            Log::error('Set time error: ' . $e->getMessage());
            return back()->with('error', 'Failed to synchronize device time: ' . $e->getMessage());
        }
    }

    /**
     * Get current system time
     */
    public function getSystemTime(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'system_time' => now()->format('d-m-Y H:i:s'),
            'timezone' => config('app.timezone')
        ]);
    }
    
    public function syncTime()
    {
        try {
            $this->zkService->syncTime();
            return response()->json(['success' => true, 'message' => 'Device time synchronized successfully.']);
        } catch (\Exception $e) {
            Log::error('Sync time error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to synchronize device time: ' . $e->getMessage()]);
        }
    }

    public function restart()
    {
        $this->zk->connect();
        $this->zk->restart();
        $this->zk->disconnect();

        return back()->with('success', 'Device Restarted Successfully');
    }

    public function shutdown()
    {
        $this->zk->connect();
        $this->zk->shutdown();
        $this->zk->disconnect();

        return back()->with('success', 'Device Shutdown Successfully');
    }

public function deviceAction($action)
{
    try {

        switch ($action) {

            case 'sync-time':
                $this->zkService->syncTime();
                $message = "Device time synchronized.";
                break;

            case 'clear-admin':
                $this->zkService->clearAdmin();
                $message = "Admin privileges cleared.";
                break;

            case 'delete-fingerprint':
                $this->zkService->deleteAllFingerprints();
                $message = "All fingerprints deleted.";
                break;

            case 'delete-transaction':
                $this->zkService->deleteAllTransactions();
                $message = "All transactions deleted.";
                break;

            case 'delete-employee':
                $this->zkService->deleteAllUsers();
                $message = "All employees deleted.";
                break;

            case 'delete-all':
                $this->zkService->clearAllData();
                $message = "All device data cleared.";
                break;

            case 'backup-employee':
                $this->zkService->backupEmployees();
                $message = "Employee backup completed.";
                break;

            case 'backup-all':
                $this->zkService->backupAllData();
                $message = "Full device backup completed.";
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid device action.'
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);

    } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}


}