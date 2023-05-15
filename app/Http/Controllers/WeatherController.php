<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function getForecast(Request $request)
    {
        return true;
    }
}
