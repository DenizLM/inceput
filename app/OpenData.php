<?php

namespace App;

use http\Client;
use Illuminate\Support\Facades\Http;

class OpenData
{
    private string $apiKey = 'ISSGAxXKjfa8J7LDMYOhj9fEPuZKZeRO5Q69qpW0';

    public array $options = [];

    public function __construct()
    {
        $this->options = [
            'X-API-KEY' => $this->apiKey,
            'X-Agency-Id' => 1
        ];
    }

    public function getVehicles()
    {
        $response = Http::withHeaders($this->options)->get('https://api.tranzy.dev/v1/opendata/vehicles')->body();

        return json_decode($response, true);
    }

    public function getRoutes()
    {
        $response = Http::withHeaders($this->options)->get('https://api.tranzy.dev/v1/opendata/routes')->body();

        return json_decode($response, true);
    }
}
