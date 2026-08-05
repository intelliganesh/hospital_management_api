<?php


namespace App\Services;

use App\Models\LogActivity;
class LogActivityService
{
    public function addToLog($subject, $errorlog, $statusCode)
    {
        $request = request();
        $log = [
            'subject' => $subject,
            'status_type' => ($statusCode == 200) ? "Success" : 'Error',
            'status_code' => is_numeric($statusCode) ? (int) $statusCode : 500,
            'log' => $errorlog,
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'agent' => $request->header('user-agent'),
            'user_id' => auth()->check() ? auth()->user()->id : null,
        ];
        LogActivity::create($log);
    }

    public function logActivityLists()
    {
        return LogActivity::latest()->get();
    }

    public function logActivityClear()
    {
        return LogActivity::truncate();
    }
}