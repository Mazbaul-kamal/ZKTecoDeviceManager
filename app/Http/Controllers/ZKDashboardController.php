<?php

namespace App\Http\Controllers;

use Rats\Zkteco\Lib\ZKTeco;
use App\Services\ZkService;
use Illuminate\Http\ZKBioService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Device;
use App\Models\ZkUser;
use Illuminate\Support\Facades\Log;

class ZKDashboardController extends Controller
{
    protected $zkService;

    public function __construct(ZkService $zkService)
    {
        $this->zkService = $zkService;
    }

    /**
     * Display the dashboard
     */
    public function dashboard()
    {
        try {
            $data = $this->zkService->getDeviceData();
            return view('dashboard', $data);
        } catch (\Exception $e) {
            Log::error('Dashboard error: ' . $e->getMessage());
            return view('dashboard', [
                'connection' => 'Failed',
                'device_info' => [],
                'users_count' => 0,
                'attendance_count' => 0,
                'sample_users' => [],
                'sample_attendance' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Fetch attendance records
     */
    public function fetch()
    {
        $ip = env('ZK_DEVICE_IP', '192.168.1.215');
        $port = env('ZK_DEVICE_PORT', 4370);

        $zk = new ZKTeco($ip, $port);
        
        try {
            $connected = $zk->connect();

            if (!$connected) {
                return response()->json([
                    'success' => false,
                    'error' => 'Device not connected'
                ], 500);
            }

            $attendance = $zk->getAttendance();
            $zk->disconnect();

            return response()->json([
                'success' => true,
                'attendance' => $attendance,
                'count' => count($attendance)
            ]);

        } catch (\Throwable $e) {
            Log::error('Fetch attendance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch users from device
     */
    public function fetchUsers()
    {
        $ip = env('ZK_DEVICE_IP', '192.168.1.215');
        $port = env('ZK_DEVICE_PORT', 4370);

        $zk = new ZKTeco($ip, $port);

        try {
            $connected = $zk->connect();

            if (!$connected) {
                return response()->json([
                    'success' => false,
                    'error' => 'Device not connected'
                ], 500);
            }

            $users = $zk->getUser();
            $zk->disconnect();

            return response()->json([
                'success' => true,
                'users' => $users,
                'count' => count($users),
            ]);

        } catch (\Throwable $e) {
            Log::error('Fetch users error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // /**
    //  * Full device test
    //  */
    // public function fullTest()
    // {
    //     $ip = env('ZK_DEVICE_IP', '192.168.1.215');
    //     $port = env('ZK_DEVICE_PORT', 4370);

    //     $zk = new ZKTeco($ip, $port);

    //     try {
    //         if (!$zk->connect()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'error' => 'Device not connected'
    //             ], 500);
    //         }

    //         $zk->disableDevice();

    //         // Device Info
    //         $deviceInfo = [
    //             'ip' => $ip,
    //             'port' => $port,
    //             'device_name' => $zk->deviceName(),
    //             'os_version' => $zk->osVersion(),
    //             'version' => $zk->version(),
    //             'serial_number' => $zk->serialNumber(),
    //             'fm_version' => $zk->fmVersion(),
    //             'platform' => $zk->platform(),
    //             'device_time' => $zk->getTime(),
    //         ];

    //         // Users and Attendance
    //         $users = $zk->getUser() ?? [];
    //         $attendance = $zk->getAttendance() ?? [];

    //         $zk->enableDevice();
    //         $zk->disconnect();

    //         return response()->json([
    //             'success' => true,
    //             'connection' => 'Success',
    //             'device_info' => $deviceInfo,
    //             'users_count' => count($users),
    //             'attendance_count' => count($attendance),
    //             'sample_users' => array_slice($users, 0, 5),
    //             'sample_attendance' => array_slice($attendance, 0, 5),
    //         ]);

    //     } catch (\Throwable $e) {
    //         Log::error('Full test error: ' . $e->getMessage());
    //         return response()->json([
    //             'success' => false,
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    
    /**
     * Display users list
     */
    public function index(Device $device)
    {
        $users = ZkUser::where('device_id', $device->id)
            ->paginate(15);

        return view('zk.users.index', compact('users', 'device'));
    }
    
    /**
     * Store new user
     */
    public function store(Request $request)
    {
        try {
            $device = Device::findOrFail($request->device_id);

            $result = $this->zkService->createUser(
                $request->uid,
                $request->userid,
                $request->name,
                $request->password ?? '',
                $request->role ?? 0,
                $request->cardno ?? 0
            );

            if ($result) {
                $this->zkService->syncUsers($device);
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create user'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Store user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
/**
 * Sync device time with system time
 */
public function syncTime(Request $request)
{
    try {
        $systemTime = $request->input('system_time', now()->format('Y-m-d H:i:s'));
        
        $result = $this->zkService->syncDeviceTime($systemTime);
        
        return response()->json($result);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Get current system time
     */
    public function getSystemTime(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'system_time' => now()->format('Y-m-d H:i:s'),
            'timezone' => config('app.timezone')
        ]);
    }
}