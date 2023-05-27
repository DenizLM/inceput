<?php

namespace App\Http\Controllers;

use App\OpenData;

class StationsController extends Controller
{
    public function __construct(public OpenData $openData){}

    // get all stops and send them with the view
    public function index()
    {
        $stops = $this->openData->getStops()->sortBy(fn ($stop) => $stop->stop_name)->unique('stop_name');

        return view('stations', compact('stops'));
    }
}

