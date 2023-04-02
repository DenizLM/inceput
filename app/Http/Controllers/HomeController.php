<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\OpenData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

}

