<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>The Weather Forecast</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins';
            background: #1a202c;
            color: white !important;
        }

        .header {
            display: flex;
            padding: 15px 0;
        }

        .btn-search {
            position: absolute;
            background: transparent;
            border-style: none;
            height: 100%;
            top: 0;
            right: 0;
        }

        input {
            color: white !important;
        }

        .loader {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            background: #eeeeee7d;
            z-index: 1;
            display: none;
        }

        .loader img {
            height: 100px;
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            margin: auto;
            top: 0;
            border-radius: 10px;
        }

        .forecast-data {
            display: flex;
            justify-content: space-around;
            gap: 5px;
            margin-top: 45px;
            flex-wrap: wrap;
        }

        .forecast-data .card {
            background: #f8f9faeb !important;
            color: #333 !important;
            flex: 0 0 18%;
        }

        @media (max-width:991px) {
            .forecast-data .card {
                flex: 0 0 25%;
            }
        }

        @media (max-width:768px) {
            .forecast-data .card {
                flex: 0 0 48%;
            }
        }
    </style>
</head>

<body class="antialiased">
    <div class="loader">
        <img src="/images/rain.gif" alt="Loader">
    </div>
    <div class="container">
        <header class="header">
            <h2>The Weather Forecast</h2>
        </header>
        <div class="row py-5 justify-content-center align-items-center">
            <div class="col-md-8">
                <div class="position-relative">
                    <form id="location-forecast">
                        @csrf
                        <input type="text" placeholder="Enter Location" id="location-search"
                            class="form-control bg-transparent">
                        <button type="submit" class="btn-search" id="search"><img src="/images/weather-forecast.png"
                                alt="Location"></button>
                    </form>
                </div>
            </div>
        </div>
        <div class="forecast-data" id="forecast-data">

        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#location-search').focus();
        });
        $(document).on('keypress', '#location-search', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault(); // Prevent form submission or page reload
                $('.loader').show();
                setTimeout(() => {
                    handleEnterPress();
                }, 500);
            }
        });

        function handleEnterPress() {
            var form_data = new FormData($('#location-forecast')[0]);
            form_data.append('location', $('#location-search').val());

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: "{{ route('forecast.get') }}",
                type: "POST",
                data: form_data,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                success: function(res) {
                    $('.loader').hide();
                    var html = '';
                    if (res.code == '404') {
                        $('#forecast-data').html(
                            '<p>Unfortunately no data found from provided location. Please try another one.</p>'
                        );
                    } else {

                        if (res.response != null) {
                            var res = res.response;
                            for (let index = 0; index < 5; index++) {
                                const date = new Date(res.days[index]?.datetime);
                                const options = {
                                    weekday: 'long'
                                };
                                const dayString = date.toLocaleString('en-US', options);

                                html += `<div class="card text-center border-dark">
                                    <div class="card-header border-dark py-3">
                                        <div class="card-title mb-0">${dayString}/${res.days[index]?.datetime}</div>
                                    </div>
                                    <div class="card-body">
                                        <img src="/images/${res.days[index]?.icon}.svg" alt="" height="100" width="100">
                                        <div class="card-desc pt-4 px-2">
                                            <p>${res.days[index]?.description}</p>
                                            <p><strong>Temperature: </strong>${res.days[index]?.temp}°C <br> <small>(${res.days[index]?.conditions})</small></p>
                                            <p><strong>Wind Speed: </strong>${res.days[index]?.windspeed}</p>
                                        </div>
                                    </div>
                                </div>`;

                            }

                            $('#forecast-data').html(html);
                        }
                    }
                },
                error: function(err) {
                    console.log(err);
                    $('.loader').hide();
                }
            });
        }
    </script>
</body>

</html>
