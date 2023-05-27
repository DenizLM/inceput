<?php

namespace App\Http\Controllers;

use App\OpenData;

class RoutesController extends Controller
{
    public function __construct(public OpenData $openData){}

    // get all stops and send them with the view
    public function index()
    {
        $routes = $this->openData->getRoutes();

        return view('routes', compact('routes'));
    }
}

