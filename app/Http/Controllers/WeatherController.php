<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\API;

class WeatherController extends Controller
{
    
    public function index()
    {
        return view('welcome');
    }

    public function getForecast(Request $request)
    {
        $payload = [
            'lat' => $request->lat,
            'long' => $request->long,
        ];
        $response = API::call('GET', $payload);
        if ($response == null) {
            return response(['response' => 'No Data Found', 'code' => '404'], 200);
        } else {
            return response(['response' => $response, 'code' => '200'], 200);
        }
    }
}
