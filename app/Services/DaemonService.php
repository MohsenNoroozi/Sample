<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class DaemonService
{
    /* Sample */
    const url = "http://10.10.10.10:10101/";

    /**
     * @param $file
     * @param $delimiter
     * @param $list_id
     * @return Response
     */
    public static function pass_list_details($file, $delimiter, $list_id): Response
    {
        return Http::get(self::url, [
            'file' => $file,
        ]);
    }


    public static function gen_partial_list($list_id): Response
    {
        return Http::get(self::url, [
            'list_id' => $list_id,
        ]);
    }

    /**
     * @param $email
     * @param $user_id
     * @param $ip
     * @return Response
     */
    public static function quick_clean($email, $user_id, $ip): Response
    {
        return Http::get(self::url, [
            'type' => 'quick_clean',
            'email' => $email,
            'user_id' => $user_id,
            'ip' => $ip,
        ]);
    }

    /**
     * @param $email
     * @param $user_id
     * @param $ip
     * @return Response
     */
    public static function deep_clean($email, $user_id, $ip): Response
    {
        return Http::get(self::url, [
            'type' => 'deep_clean',
            'email' => $email,
            'user_id' => $user_id,
            'ip' => $ip,
        ]);
    }
}
