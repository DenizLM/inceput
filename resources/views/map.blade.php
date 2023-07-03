@extends('layouts.base')

@section('content')

    <button style="position: absolute; top:10vh;left:5vw;z-index: 15;" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
        Filter
    </button>

    <div class="bg-white" style="position: absolute; top:0; height: 8vh;width: 100%;z-index: 15;">
        <div style=" background-color: rgba(62,84,172,0.09)">
            <!-- back button -->
            <div onclick="window.location.href = '{{ route('home') }}'" class="text-center" style="position: absolute; top:2vh;left:5vw;">
                <i class="fa-solid fa-arrow-left fa-2x"></i>
            </div>
            <div class="text-center pt-2 row" style="height: 8vh">
                <h1 class="fw-bolder display-2 w-100" style="letter-spacing: -3px"> CITY GUIDE</h1>
            </div>
        </div>
    </div>

    <div id="map" style="width: 100vw; height: 100vh">
    </div>
@endsection

<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Choose a route name</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="exampleInputEmail1" class="form-label">Type route name</label>
                    <input type="text" class="form-control" id="route_name" aria-describedby="emailHelp">
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="direction">
                    <label class="form-check-label" for="direction">Direction</label>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="addFilter()">Save changes</button>
            </div>
        </div>
    </div>
</div>


@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        var myModal = new bootstrap.Modal(document.getElementById('filterModal'), {
            keyboard: false
        })
        const url = new URL(window.location);
        if (url.searchParams.has('route') && url.searchParams.has('direction')) {
            $('#route_name').val(url.searchParams.get('route'));
            $('#direction').prop('checked', url.searchParams.get('direction') == '1');
        }

        var polyLine = null;

        function addFilter() {
            const url = new URL(window.location);

            if (!$('#route_name').val()) {
                url.searchParams.delete('route');
                url.searchParams.delete('direction');
            } else {
                url.searchParams.set('route', $('#route_name').val());
                url.searchParams.set('direction', $('#direction').is(':checked') ? '1' : '0');
            }

            window.history.pushState(null, '', url.toString());
            getCoordinates();
            myModal.toggle();
        }

        let markers = [];
        let stationMarkers = [];
        let map = {}
        function initMap() {
            var mapProp= {
                center:new google.maps.LatLng(47.1585, 27.6014),
                zoom:12,
                disableDefaultUI: true,
                mapId: 'a70b8a4a5466984c',

            };
            map = new google.maps.Map(document.getElementById('map'),mapProp);

        }

        function addStationsToMap(stops) {

            for (let marker of stationMarkers) {
                marker.setMap(null);
            }
            stationMarkers = [];

            for (let stop of stops) {
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng(stop.lat, stop.lon),
                    map: map
                })
            }
            var labelOriginFilled =  new google.maps.Point(12,9);
            for (let stop of stops) {
                stationMarkers.push(new google.maps.Marker({
                    position: new google.maps.LatLng(stop.stop_lat, stop.stop_lon),
                    map: map,
                    icon: {
                        path: "M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2z",
                        fillColor: "#ffffff",
                        fillOpacity: 1,
                        strokeWeight: 1,
                        strokeColor: "#000000",
                        scale: 0.5,
                        anchor: labelOriginFilled
                    },
                }))
            }
        }

        function drawPolyline(shape) {
            var points = [];
            for (let i = 0; i < shape.length; i++) {
                points.push(new google.maps.LatLng(shape[i].shape_pt_lat, shape[i].shape_pt_lon));
            }

            if (polyLine) {
                polyLine.setMap(null);
            }

            polyLine = new google.maps.Polyline({
                path: points,
                geodesic: true,
                strokeColor: '#FF0000',
                strokeOpacity: 1.0,
                strokeWeight: 4
            });


            polyLine.setMap(map);
        }

        async function getCoordinates() {
            const url = new URL(window.location);
            var response = null;

            if (url.searchParams.has('route') && url.searchParams.has('direction')) {
                 response = await fetch('{{ route('get-coordinates') }}?route=' + url.searchParams.get('route') + '&direction=' + url.searchParams.get('direction'));
            } else {
                 response = await fetch('{{ route('get-coordinates') }}');
            }
            const jsonResponse = await response.json();

            const vehicles = jsonResponse.vehicles;

            const shape = jsonResponse.shape;

            if (shape) {
                drawPolyline(shape);
                addStationsToMap(jsonResponse.stops);
            }

            for (let marker of markers) {
                marker.setMap(null);
            }
            markers = [];

            for (let vehicle of vehicles) {
                var pinColor =  vehicle.route_color;
                var pinLabel = vehicle.route_name;

                // Pick your pin (hole or no hole)
                var pinSVGHole = "M12,11.5A2.5,2.5 0 0,1 9.5,9A2.5,2.5 0 0,1 12,6.5A2.5,2.5 0 0,1 14.5,9A2.5,2.5 0 0,1 12,11.5M12,2A7,7 0 0,0 5,9C5,14.25 12,22 12,22C12,22 19,14.25 19,9A7,7 0 0,0 12,2Z";
                var labelOriginHole = new google.maps.Point(12,15);
                var pinSVGFilled = "M 12,2 C 8.1340068,2 5,5.1340068 5,9 c 0,5.25 7,13 7,13 0,0 7,-7.75 7,-13 0,-3.8659932 -3.134007,-7 -7,-7 z";
                var labelOriginFilled =  new google.maps.Point(12,9);


                var markerImage = {
                    path: pinSVGFilled,
                    anchor: new google.maps.Point(12,17),
                    fillOpacity: 1,
                    fillColor: pinColor,
                    strokeWeight: 2,
                    strokeColor: "white",
                    scale: 2,
                    labelOrigin: labelOriginFilled
                };
                var label = {
                    text: pinLabel,
                    color: "white",
                    fontSize: "12px",
                };

                marker  = new google.maps.Marker({
                    map: map,
                    label: label,
                    position: {
                        lat: parseFloat(vehicle.latitude),
                        lng: parseFloat(vehicle.longitude)
                    },
                    icon: markerImage,
                    //OPTIONAL: animation: google.maps.Animation.DROP,
                });

                markers.push(marker);
            }
        }

        getCoordinates();

       // getStops();

        setInterval(function () {
            getCoordinates();
        }, 10000);

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBBEN1yjz4sdeQX0GNcNDYojMd_DPclNuE&v=beta&libraries=marker&callback=initMap"></script>
@endsection
