<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZKBioService;
use App\Services\ZkService;
use Illuminate\Http\JsonResponse;
use App\Models\Device;
use App\Models\ZkUser;
use Illuminate\Support\Facades\Log;

class ZkUserController extends Controller
{
    protected $zk;

    public function __construct(ZKService $zk)
    {
        $this->zk = $zk;
    }

    public function index()
    {
        $users = $this->zk->getUsers();
        return view('zk.users.index', compact('users'));
    }

    public function create()
    {
        return view('zk.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'uid' => 'required|numeric',
            'userid' => 'required',
            'name' => 'required'
        ]);

        $this->zk->createUser(
            $request->uid,
            $request->userid,
            $request->name,
            $request->password ?? '',
            $request->role ?? 0,
            $request->cardno ?? 0
        );

        return redirect()->route('zk.users.index')
            ->with('success', 'User created successfully');
    }

    public function edit($uid)
    {
        $users = $this->zk->getUsers();
        $user = collect($users)->firstWhere('uid', $uid);

        return view('zk.users.edit', compact('user'));
    }

    public function update(Request $request, $uid)
    {
        $this->zk->updateUser(
            $uid,
            $request->userid,
            $request->name,
            $request->password ?? '',
            $request->role ?? 0,
            $request->cardno ?? 0
        );

        return redirect()->route('zk.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy($uid)
    {
        $this->zk->deleteUser($uid);

        return redirect()->route('zk.users.index')
            ->with('success', 'User deleted successfully');
    }
    public function registerFingerprint($uid)
{
    $device = Device::first(); // or selected device

    if (!$this->ZKService->connectDevice($device)) {
        return response()->json(['success' => false]);
    }

    $this->ZKService->zk->startEnroll($uid);

    $this->ZKService->zk->disconnect();

    return response()->json(['success' => true]);
}
public function enrollFingerprintMode($uid)
{
    $ip = config('zk.ip');
    $port = config('zk.port');

    $zk = new \Rats\Zkteco\Lib\ZKTeco($ip, $port);
    
    if (!$zk->connect()) {
        return response()->json(['error' => 'Device not connected']);
    }

    // Disable device temporarily
    $zk->disableDevice();

    // Display instruction on device LCD
    $zk->writeLCD(2, "User {$uid}: Scan Finger");

    // Re-enable device
    $zk->enableDevice();

    $zk->disconnect();

    return response()->json(['success' => true, 'message' => "Device is ready for fingerprint enrollment for UID {$uid}"]);
}

    /** Trigger device fingerprint enrollment */
    public function enrollFingerprint($uid)
    {
        if(!$this->zk->connect()) {
            return response()->json(['error' => 'Device not connected']);
        }

        $this->zk->disableDevice();
        $this->zk->writeLCD(2, "UID {$uid}: Scan Finger");
        $this->zk->enableDevice();
        $this->zk->disconnect();

        return response()->json(['success' => true, 'message' => "Device ready for fingerprint enrollment for UID {$uid}"]);
    }

    public function liveAttendance(ZKService $zk)
{
    $logs = $zk->getLatestAttendance(20);

    $html = view('partials.attendance_live', ['attendance' => $logs])->render();

    return response()->json(['html' => $html]);
}

}
