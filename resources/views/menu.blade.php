@extends('layouts.base')

@section('content')
    <div class="text-center pt-4">
        <h1 class="fw-bolder display-2" style="letter-spacing: -3px">CITY GUIDE</h1>
    </div>

    <div class="container pt-4">
        <div class="row mb-2 text-center">
            <div onclick="window.location.href = '/map'" class="col-6 mb-2">
                <div class="bg-white rounded text-center shadow"  style="height: 20vh">
                    <span class="w-75">
                        <img class="rounded" style="height: 15vh; width: auto" src="assets/img/menu/map-button.png" alt="">
                    </span>
                    <span>
                        Live map
                    </span>
                </div>
            </div>
            <div class="col-6">
                <div onclick="window.location.href = '/stations'" class="bg-white rounded text-center shadow"  style="height: 20vh">
                    <span class="w-75" style="height: 75px">
                        <img class="rounded" style="height: 15vh; width: auto" src="assets/img/menu/menu-station.png" alt="">
                    </span>
                    <span>
                        Stations
                    </span>
                </div>
            </div>
            <div class="col-6">
                <div onclick="window.location.href = '/routes'" class="bg-white rounded text-center shadow"  style="height: 20vh">
                    <span class="w-75" style="height: 75px">
                        <img class="rounded" style="height: 15vh; width: auto" src="assets/img/menu/menu-route.png" alt="">
                    </span>
                    <span>
                        Routes
                    </span>
                </div>
            </div>
            <div class="col-6">
                <div onclick="window.location.href = '/stats'"  class="bg-white rounded text-center shadow"  style="height: 20vh">
                    <span class="w-75" style="height: 75px">
                        <img class="rounded" style="height: 15vh; width: auto" src="assets/img/menu/menu-stats.png" alt="">
                    </span>
                    <span>
                        Fun stats
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div>
        <img src="assets/img/menu/menu-vector.png" alt="">
    </div>

@endsection

@section('scripts')

@endsection
