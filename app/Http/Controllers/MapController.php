<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\OpenData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MapController extends Controller
{
    public function __construct(public OpenData $openData)
    {
    }

    public function index()
    {
        $routes = $this->openData->getRoutes();

        return view('map', compact('routes'));
    }
}

