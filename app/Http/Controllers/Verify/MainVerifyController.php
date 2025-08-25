<?php

namespace App\Http\Controllers\Verify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class MainVerifyController
{
    protected static $verificationMap = [
        'user' => UsersVerifyController::class,
    ];

    public static function handle(string $method, string $type, Request $request)
    {
        $controller = App::make(self::$verificationMap[$type]);
        $response = empty($controller->$method($request)->original) ? null : $controller->$method($request)->original;
        if ($response) {
            http_response_code(400); // ✅ رجّع كود الحالة 400
            header('Content-Type: application/json'); // ✅ خلي نوع المحتوى JSON
            echo json_encode($response); // ✅ اطبع الرسالة
            exit(); // ✅ اقفل السكربت
        }
        return true;
    }
    public static function __callStatic($method, $parameters)
    {
        if (count($parameters) < 3) {
            return function ($request) use ($method, $parameters) {
                return self::handle($method, $parameters[1], $request);
            };
        }
        $request = $parameters[2];
        $type = $parameters[1];
        return self::handle($method, $type, $request);
    }
}
