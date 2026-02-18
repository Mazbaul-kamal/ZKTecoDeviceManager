<?php

namespace App\Services;

use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\ZkUser;
use Illuminate\Support\Facades\Log;


class ZkService
{
    protected ZKTeco $zk;
    protected string $ip;
    protected int $port;

    public function __construct()
    {
        $this->ip = config('zk.ip', '192.168.1.215');
        $this->port = config('zk.port', 4370);
        $this->zk = new ZKTeco($this->ip, $this->port);
    }

    public function connect()
    {
        return $this->zk->connect();
    }

    public function disconnect()
    {
        return $this->zk->disconnect();
    }

    public function connectDevice(Device $device): bool
    {
        $this->ip = $device->ip;
        $this->port = $device->port;
        $this->zk = new ZKTeco($this->ip, $this->port);
        return $this->zk->connect();
    }

    public function getDeviceData()
    {
        if (!$this->connect()) {
            return [
                'connection' => 'Failed',
                'device_info' => [],
                'users_count' => 0,
                'attendance_count' => 0,
                'sample_users' => [],
                'sample_attendance' => [],
            ];
        }

        $deviceInfo = [
            'Device IP' => $this->ip,
            'Port' => $this->port,
            'device_name' => $this->zk->deviceName(),
            'serial_number' => $this->zk->serialNumber(),
            'platform' => $this->zk->platform(),
            'firmware_version' => $this->zk->fmVersion(),
            'os_version' => $this->zk->osVersion(),
            'version' => $this->zk->version(),
            'device_time' => $this->zk->getTime(),
        ];

        $users = $this->zk->getUser() ?? [];
        $attendance = $this->zk->getAttendance() ?? [];

        $this->disconnect();

        return [
            'connection' => 'Success',
            'device_info' => $deviceInfo,
            'users_count' => count($users),
            'attendance_count' => count($attendance),
            'sample_users' => array_slice($users, 0, 1000),
            'sample_attendance' => array_slice($attendance, 0, 1000),
        ];
    }
    public function restart()
    {
        if (!$this->connect()) {
            return false;
        }

        $result = $this->zk->restart();
        $this->disconnect();
        return $result;

    }
    public function getAllUsers()
    {
        if (!$this->connect()) return [];
        $users = $this->zk->getUser();
        $this->disconnect();
        return $users;
    }

    public function getUsers()
    {
        if (!$this->connect()) return [];
        $users = $this->zk->getUser();
        $this->disconnect();
        return $users ?? [];
    }

    public function syncUsers(Device $device)
    {
        if (!$this->connectDevice($device)) return false;

        $users = $this->zk->getUser();
        $this->zk->disconnect();

        foreach ($users as $user) {
            \App\Models\ZkUser::updateOrCreate(
                [
                    'device_id' => $device->id,
                    'uid' => $user['uid'],
                ],
                [
                    'userid' => $user['userid'],
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'cardno' => $user['cardno'] ?? null,
                ]
            );
        }

        return true;
    }

    public function createUser($uid, $userid, $name, $password = '', $role = 0, $cardno = 0)
    {
        if (!$this->connect()) return false;

        $result = $this->zk->setUser(
            $uid,
            $userid,
            $name,
            $password,
            $role,
            $cardno
        );

        $this->disconnect();
        return $result;
    }

    public function updateUser($uid, $userid, $name, $password = '', $role = 0, $cardno = 0)
    {
        return $this->createUser($uid, $userid, $name, $password, $role, $cardno);
    }

    public function deleteUser($uid)
    {
        if (!$this->connect()) return false;

        $result = $this->zk->removeUser($uid);
        $this->disconnect();
        return $result;
    }

    public function getAttendance()
    {
        if (!$this->connect()) return [];
        $logs = $this->zk->getAttendance();
        $this->disconnect();
        return $logs;
    }

    public function syncAttendance()
    {
        $logs = DB::connection('zktime')
            ->table('att_log')
            ->select('pin', 'verify_time')
            ->get();

        foreach ($logs as $log) {
            $date = date('Y-m-d', strtotime($log->verify_time));
            $time = date('H:i:s', strtotime($log->verify_time));

            Attendance::updateOrCreate(
                [
                    'emp_code' => $log->pin,
                    'date' => $date,
                ],
                [
                    'check_in' => $time,
                    'status' => 'Present'
                ]
            );
        }
    }

    /** Fingerprint */
    public function parseFingerprint($rawTemplate)
    {
        return $this->zk->parseFingerprintTemplate($rawTemplate);
    }

    public function enrollFingerprint($uid, $fingerId, $templateData)
    {
        return $this->zk->enrollFingerprint($uid, $fingerId, $templateData);
    }

    /** Face */
    public function getFaceData($uid)
    {
        return $this->zk->getFaceData($uid);
    }

    public function setFaceData($uid, $faceTemplates)
    {
        return $this->zk->setFaceData($uid, $faceTemplates);
    }

    public function enrollFace($uid, $templateData)
    {
        return $this->zk->enrollFaceTemplate($uid, $templateData);
    }

    /** Card */
    public function getUserCard($uid)
    {
        return $this->zk->getUserCardNumber($uid);
    }

    /** Role */
    public function setUserRole($uid, $level, $permissions = [])
    {
        return $this->zk->setUserRole($uid, $level, $permissions);
    }

    public function getUserRole($uid)
    {
        return $this->zk->getUserRole($uid);
    }

    public function getAvailableRoles()
    {
        return $this->zk->getAvailableRoles();
    }
/**
 * Sync device time with system time
 *
 * @param string $systemTime
 * @return array
 */
public function syncDeviceTime(string $systemTime = null): array
{
    try {
        $timeToSet = $systemTime ?? now()->format('Y-m-d H:i:s');
        
        // Connect to device
        if (!$this->connect()) {
            return [
                'success' => false,
                'message' => 'Could not connect to device'
            ];
        }

        // Set the time directly (some devices don't need disable/enable)
        $this->zk->setTime($timeToSet);
        
        // Wait a moment
        usleep(500000);
        
        // Get updated time
        $deviceTime = $this->zk->getTime();
        
        $this->disconnect();

        return [
            'success' => true,
            'device_time' => $deviceTime ?: $timeToSet
        ];
        
    } catch (\Exception $e) {
        return [
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ];
    }
}
}