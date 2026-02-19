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
    
    /**
     * Sync device time with server time.
     */
    public function syncTime()
    {
        $timeString = now()
            ->setTimezone(config('app.timezone'))
            ->format('d-m-y H:i:s');

        $result = $this->zkService->setDeviceTime($timeString);

        return redirect()->back()->with([
            'sync_success' => $result['success'],
            'sync_message' => $result['message'] ?? null,
            'device_time'  => $result['device_time'] ?? null,
        ]);
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
                $this->fingerprintService->syncTime();
                $message = "Device time synchronized.";
                break;

            case 'clear-admin':
                $this->fingerprintService->clearAdmin();
                $message = "Admin privileges cleared.";
                break;

            case 'delete-fingerprint':
                $this->fingerprintService->deleteAllFingerprints();
                $message = "All fingerprints deleted.";
                break;

            case 'delete-transaction':
                $this->fingerprintService->deleteAllTransactions();
                $message = "All transactions deleted.";
                break;

            case 'delete-employee':
                $this->fingerprintService->deleteAllUsers();
                $message = "All employees deleted.";
                break;

            case 'delete-all':
                $this->fingerprintService->clearAllData();
                $message = "All device data cleared.";
                break;

            case 'backup-employee':
                $this->fingerprintService->backupEmployees();
                $message = "Employee backup completed.";
                break;

            case 'backup-all':
                $this->fingerprintService->backupAllData();
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