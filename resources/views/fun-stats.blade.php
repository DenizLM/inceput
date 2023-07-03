@extends('layouts.base')

@section('content')
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
    <div class="p-4">
        <select class="form-select mb-2" id="stats_select" style="margin-top: 9vh">
            <option value="" selected>Select the stats you want to be displayed</option>
            <option value="speed">Average speed</option>
            <option value="vehicles_count">Number of vehicles</option>
            <option value="distance">Distance traveled</option>
        </select>

        <div class="speed py-2 chart" id="resizable_desc" style="height: 370px;border:1px solid gray">
            <div id="chart_container_average_speed_desc" style="height: 100%; width: 100%;"></div>
        </div>

        <div class="speed py-2 chart" id="resizable_asc" style="height: 370px;border:1px solid gray;margin-top: 10px">
            <div id="chart_container_average_speed_asc" style="height: 100%; width: 100%;"></div>
        </div>

        <div class="vehicles_count py-2 chart" id="busses_count" style="height: 340px;border: 1px solid grey">
            <div id="chart_container_busses_count" style="height: 100%; width: 100%;"></div>
        </div>

        <div class="vehicles_count py-2 chart" id="tram_count" style="height: 340px;border: 1px solid grey">
            <div id="chart_container_tram_count" style="height: 100%; width: 100%;"></div>
        </div>

        <div class="distance chart" id="distance_chart_container" style="height: 2500px; width: 100%;"></div>
    </div>

@endsection

@section('scripts')
    <link href="https://canvasjs.com/assets/css/jquery-ui.1.11.2.min.css" rel="stylesheet" />
    <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery-ui.1.11.2.min.js"></script>
    <script src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>
    <script>
        $(function () {
            var distanceChart = new CanvasJS.Chart("distance_chart_container", {
                theme: "light2",
                animationEnabled: true,
                title: {
                    text: "Distance traveled"
                },
                axisY2: {
                    title: "Distance in km",
                    titleFontSize: 14,
                    includeZero: true,
                    suffix: "km",
                },
                axisX: {
                    margin: 10,
                    labelPlacement: "inside",
                    tickPlacement: "inside"
                },
                data: [{
                    type: "bar",
                    yValueFormatString: "#.###km",
                    indexLabel: "{y}",
                    dataPoints: [
                        @foreach ($distances as $key => $distance)
                            { label: "{{ $distance['route_label'] }}", y: {{ $distance['length'] }} },
                        @endforeach
                    ]
                }]
            });
            distanceChart.render();

            var optionsBussesCount = {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "Number of vehicles"
                },
                axisX: {
                    title: "Bus number"
                },
                data: [{
                    type: "column",
                    dataPoints: [
                        @foreach ($bussesCount as $key => $vehicles)
                            { label: "{{ $key }}", y: {{ $vehicles }} },
                        @endforeach
                    ]
                }]
            };

            var optionsTramCount = {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "Number of vehicles"
                },
                axisX: {
                    title: "Tram number"
                },
                data: [{
                    type: "column",
                    dataPoints: [
                        @foreach ($tramsCount as $key => $vehicles)
                            { label: "{{ $key }}", y: {{ $vehicles }} },
                        @endforeach
                    ]
                }]
            };

            $("#busses_count").resizable({
                create: function (event, ui) {
                    //Create chart.
                    $("#chart_container_busses_count").CanvasJSChart(optionsBussesCount);
                },
                resize: function (event, ui) {
                    //Update chart size according to its container size.
                    $("#chart_container_busses_count").CanvasJSChart(optionsBussesCount);
                }
            });

            $("#tram_count").resizable({
                create: function (event, ui) {
                    //Create chart.
                    $("#chart_container_tram_count").CanvasJSChart(optionsTramCount);
                },
                resize: function (event, ui) {
                    //Update chart size according to its container size.
                    $("#chart_container_tram_count").CanvasJSChart(optionsTramCount);
                }
            });

            var optionsDesc = {
                animationEnabled: true,
                theme: "light2",
                axisY: {
                    title: "Average speed",
                    suffix: "km/h"
                },
                axisX: {
                    title: "Bus number"
                },
                title:{
                    text: "Average speeds of the city's buses descending"
                },
                data: [{
                    type: "column",
                    dataPoints: [
                        @foreach ($averageSpeedsDesc as $key => $averageSpeed)
                            { label: "{{ $key }}", y: {{ $averageSpeed }} },
                        @endforeach
                    ]
                }]
            };

            var optionsAsc = {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "Average speeds of the city's buses ascending"
                },
                axisY: {
                    title: "Average speed",
                    suffix: "km/h"
                },
                axisX: {
                    title: "Bus number"
                },
                data: [{
                    type: "column",
                    dataPoints: [
                        @foreach ($averageSpeedsAsc as $key => $averageSpeed)
                            { label: "{{ $key }}", y: {{ $averageSpeed }} },
                        @endforeach
                    ]
                }]
            };

            $("#resizable_desc").resizable({
                create: function (event, ui) {
                    //Create chart.
                    $("#chart_container_average_speed_desc").CanvasJSChart(optionsDesc);
                },
                resize: function (event, ui) {
                    //Update chart size according to its container size.
                    $("#chart_container_average_speed_desc").CanvasJSChart().render();
                }
            });

            $("#resizable_asc").resizable({
                create: function (event, ui) {
                    //Create chart.
                    $("#chart_container_average_speed_asc").CanvasJSChart(optionsAsc);
                },
                resize: function (event, ui) {
                    //Update chart size according to its container size.
                    $("#chart_container_average_speed_asc").CanvasJSChart().render();
                }
            })

            $('.chart').hide();

            $('#stats_select').change(function(){
                $('.chart').hide();
                if($(this).val() == 'speed') {
                    $('.chart.speed').show();
                }

                if($(this).val() == 'vehicles_count') {
                    $('.chart.vehicles_count').show();
                }

                if($(this).val() == 'distance') {
                    $('.chart.distance').show();
                }
            })
        });
    </script>
@endsection
