<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class API extends Model
{
    public static function call($method, $payload)
    {
        $data_string = $payload['lat'] . ',' . $payload['long'] . '?key=' . config('app.api_key');
        $url = config('app.api_url') . $data_string;

        $headers = array(
            'Content-Type: application/json',
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        try {
            $result = curl_exec($ch);
            $json = json_decode($result, TRUE);
        } catch (\Exception $e) {
            $response['code'] = 500; // server fault
            $response['msg'] = 'Something went wrong, please try again later.';
        }

        return $json;
    }
}
