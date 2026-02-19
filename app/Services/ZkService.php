<?php

namespace App\Services;

use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Device;
use App\Models\ZkUser;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


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
        public function restart()
    {
        if (!$this->connect()) {
            return false;
        }

        $result = $this->zk->restart();
        $this->disconnect();
        return $result;

    }

        public function shutdown()
        {
            if (!$this->connect()) {
                return false;
            }
    
            $result = $this->zk->shutdown();
            $this->disconnect();
            return $result;
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
            'Device_name' => $this->zk->deviceName(),
            'Serial_number' => $this->zk->serialNumber(),
            'Platform' => $this->zk->platform(),
            'Firmware_version' => $this->zk->fmVersion(),
            'Os_version' => $this->zk->osVersion(),
            'Version' => $this->zk->version(),
            'Device_time' => $this->zk->getTime(),
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



public function syncTime()
{
    $this->zk->connect();
    $this->zk->setTime(date('Y-m-d H:i:s'));
    $this->zk->disconnect();
}

public function clearAdmin()
{
    $this->zk->connect();
    $this->zk->clearAdmin();
    $this->zk->disconnect();
}

public function deleteAllFingerprints()
{
    $this->zk->connect();
    $this->zk->clearFingerprint();
    $this->zk->disconnect();
}

public function deleteAllTransactions()
{
    $this->zk->connect();
    $this->zk->clearAttendance();
    $this->zk->disconnect();
}

public function deleteAllUsers()
{
    $this->zk->connect();
    $this->zk->clearUser();
    $this->zk->disconnect();
}

public function clearAllData()
{
    $this->zk->connect();
    $this->zk->clearData();
    $this->zk->disconnect();
}

public function backupEmployees()
{
    $this->zk->connect();
    $users = $this->zk->getAllUserInfo();
    $this->zk->disconnect();

    \Storage::put('backup/employees.json', json_encode($users));
}

public function backupAllData()
{
    $this->zk->connect();
    $users = $this->zk->getAllUserInfo();
    $logs  = $this->zk->getAttendance();
    $this->zk->disconnect();

    \Storage::put('backup/full_backup.json', json_encode([
        'users' => $users,
        'logs'  => $logs
    ]));
}

}
