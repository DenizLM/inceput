<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoordinatesController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all();

        return response()->json($vehicles);
    }

}

