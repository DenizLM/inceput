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
    <div id="resizable_desc" style="height: 370px;border:1px solid gray;margin-top: 8vh">
        <div id="chart_container_average_speed_desc" style="height: 100%; width: 100%;"></div>
    </div>

    <div id="resizable_asc" style="height: 370px;border:1px solid gray;margin-top: 10px">
        <div id="chart_container_average_speed_asc" style="height: 100%; width: 100%;"></div>
    </div>
@endsection

@section('scripts')
    <link href="https://canvasjs.com/assets/css/jquery-ui.1.11.2.min.css" rel="stylesheet" />
    <script src="https://canvasjs.com/assets/script/jquery-1.11.1.min.js"></script>
    <script src="https://canvasjs.com/assets/script/jquery-ui.1.11.2.min.js"></script>
    <script src="https://cdn.canvasjs.com/jquery.canvasjs.min.js"></script>
    <script>
        $(function () {
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
        });
    </script>
@endsection
