<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use Illuminate\Support\Facades\Log;

class ADMSController extends Controller
{
    public function receiveAttendance(Request $request)
    {
        // Log raw data for debugging
        Log::info('ADMS DATA:', $request->all());

        /*
        ZKTeco ADMS সাধারণত নিচের format পাঠায়:

        SN=DeviceSerial
        table=ATTLOG
        Stamp=9999
        OpStamp=9999
        ATTLOG:
        1\t8888\t2025-02-20 08:55:12\t0\t0\t0\t0
        */

        $raw = $request->getContent();

        if(str_contains($raw, "ATTLOG")) {

            $lines = explode("\n", $raw);

            foreach ($lines as $line) {

                if(preg_match('/^\d+\t/', $line)) {

                    $parts = explode("\t", trim($line));

                    AttendanceLog::create([
                        'device_sn' => $request->input('SN'),
                        'uid' => $parts[0] ?? null,
                        'user_id' => $parts[1] ?? null,
                        'punch_time' => $parts[2] ?? null,
                        'status' => $parts[3] ?? 0,
                        'verify_type' => $parts[4] ?? 0,
                    ]);
                }
            }

            return response("OK", 200);
        }

        return response("No Data", 400);
    }
}
