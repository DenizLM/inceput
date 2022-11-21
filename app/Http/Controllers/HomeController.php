<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        $apiData = [];

        try {
            $apiData = json_decode(file_get_contents('https://gps.sctpiasi.ro/json'), true);
        } catch (\Exception $e) {
            Log::warning($e);
        }

        dd(debug_print_backtrace());
        return view('welcome', compact('apiData'));
    }

}
