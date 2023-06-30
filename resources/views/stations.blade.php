@extends('layouts.base')


@section('content')
    <div class="bg-white" style="position: absolute; top:0; height: 8vh;width: 100%;z-index: 15;">
        <div style=" background-color: rgba(62,84,172,0.09)">

            <div onclick="window.location.href = '{{ route('home') }}'" style="position: absolute; top:20px;left:5vw;">
                <i class="fa-solid fa-arrow-left fa-2x"></i>
            </div>
            <div class="text-center pt-2 row" style="height: 8vh">
                <h1 class="fw-bolder display-2 w-100" style="letter-spacing: -3px"> CITY GUIDE</h1>
            </div>
        </div>
    </div>
    <!-- show all stops and make a field that searches through them, showing only the ones that match using jquery -->
    <div class="container pt-4  mt-5">
        <div class="row mt-3" >
            <div class="col-12 pb-2">
                <label for="stop_name" class="form-label">Type station name</label>
                <input type="text" class="form-control" id="stop_name" aria-describedby="stop_name">
            </div>
            @foreach ($stops as $stop)
                <div class="col-12 pb-2 stop">
                    <div class="bg-white rounded text-center shadow pt-1"  style="height: 50px">
                        <p class="fs-4 fw-bolder" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap">
                            {{ $stop->stop_name }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js" integrity="sha512-pumBsjNRGGqkPzKHndZMaAG+bir374sORyzM3uulLV14lN5LyykqNk8eEeUlUkB3U0M4FApyaHraT65ihJhDpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script>
        $(document).ready(function () {
            $('#stop_name').keyup(function () {
                var stop_name = $('#stop_name').val();
                // show or hide stops based on stop_name
                $('.stop').hide();
                $('.stop').filter(function () {
                    return $(this).text().toLowerCase().indexOf(stop_name.toLowerCase()) > -1;
                }).show();
            })
        })
    </script>
@endsection

