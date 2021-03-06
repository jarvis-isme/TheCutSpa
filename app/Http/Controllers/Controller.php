<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /** key */
    const KEY_DATA = 'data';
    const KEY_MESSAGE = 'message';
    const KEY_CODE = 'code';
    const KEY_DETAIL_CODE = 'detailsCode';
    const KEY_TOKEN = 'token';
    const KEY_TOKEN_EXPIRE_IN = 'tokenExpireIn';
    const KEY_REFRESH_TOKEN = 'refreshToken';
    const KEY_REFRESH_TOKEN_EXPIRE_IN = 'refreshTokenExpireIn';

    const CODE_INVALID_FIELD = 'IER400001';
    const YOUR_ROLE_CANNOT_CALL_THIS_API = 'ERR400030';
    const M_YOUR_ROLE_CANNOT_CALL_THIS_API = 'Your role cannot call this api.';

    public static function responseST($detailsCode, $message, $data = [])
    {
        $response = [
            self::KEY_CODE => 200,
            self::KEY_DETAIL_CODE => $detailsCode,
            self::KEY_DATA => $data,
            self::KEY_MESSAGE => $message,
        ];
        return response()->json($response, 200);
    }

    public static function responseIER($message, $detailsCode = self::CODE_INVALID_FIELD)
    {
        $response = [
            self::KEY_CODE => 400,
            self::KEY_DETAIL_CODE => $detailsCode,
            self::KEY_MESSAGE => $message,
        ];
        return response()->json($response, 400);
    }

    public static function responseEX($detailsCode, $message)
    {
        $response = [
            self::KEY_CODE => 500,
            self::KEY_DETAIL_CODE => $detailsCode,
            self::KEY_MESSAGE => $message,
        ];
        return response()->json($response, 500);
    }

    public static function responseERR($detailsCode, $message, $data = [])
    {
        $response = [
            self::KEY_CODE => 400,
            self::KEY_DATA => $data,
            self::KEY_DETAIL_CODE => $detailsCode,
            self::KEY_MESSAGE => $message,
        ];
        return response()->json($response, 400);
    }

    public static function responseCommon($code, $detailsCode, $message)
    {
        $response = [
            self::KEY_CODE => $code,
            self::KEY_DETAIL_CODE => $detailsCode,
            self::KEY_MESSAGE => $message,
        ];
        return response()->json($response, $code);
    }

    public static function responseObject($response)
    {
        return response()->json($response, $response[self::KEY_CODE] ?? 400);
    }

    public function isAdmin()
    {
        $user = Auth::user();
        return $user->{User::COL_ROLE_ID} == User::ADMIN_ROLE_ID;
    }

    public function isManager()
    {
        $user = Auth::user();
        return $user->{User::COL_ROLE_ID} == User::MANAGER_ROLE_ID;
    }

    public function isCustomer()
    {
        $user = Auth::user();
        return $user->{User::COL_ROLE_ID} == User::CUSTOMER_ROLE_ID;
    }

    public function isStaff()
    {
        $user = Auth::user();
        return $user->{User::COL_ROLE_ID} == User::STAFF_ROLE_ID;
    }
}
