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
            color: black !important;
        }

        .header {
            display: flex;
            padding: 15px 0;
        }

        .btn-search {
            position: absolute;
            background: transparent;
            border-style: none;
            height: 30px;
            top: 0;
            right: 1rem;
            bottom: 0;
            margin: auto;

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
            margin-top: 35px;
            flex-wrap: wrap;
        }

        .forecast-data .card {
            background: #f8f9faa8 !important;
            color: #333 !important;
            flex: 0 0 18%;
        }

        .result-autocomplete {
            position: absolute;
            z-index: 1;
            background: white;
            color: black;
            width: 100%;
        }

        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.8;
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-control {
            padding: 1rem !important;
            background: transparent !important;
            border: 2px solid black !important;
            color: black !important;
            font-size: 20px;
        }

        .card-title p {
            font-size: 14px;
        }

        .card-desc p {
            font-size: 16px;
        }

        input::placeholder {
            /* Chrome, Firefox, Opera, Safari 10.1+ */
            color: black !important;
        }

        @media (max-width:991px) {
            .forecast-data {
                gap: 1px !important;
            }

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
    <div class="video-background">
        <video autoplay loop muted>
            <source src="/videos/bg-conver.mp4" type="video/mp4">
            <!-- Add additional video sources for cross-browser compatibility (e.g., WebM, Ogg) -->
        </video>
    </div>
    <div class="container">
        <header class="header">
            <h2>The Weather Forecast</h2>
        </header>
        <div class="row pt-5 pb-3 justify-content-center align-items-center">
            <div class="col-md-8">
                <div class="position-relative">
                    <form id="location-forecast">
                        @csrf
                        <input type="text" placeholder="Enter Location" id="location-search" class="form-control">
                        {{-- <button type="submit" class="btn-search" id="search"></button> --}}
                        <img src="/images/location.png" alt="Location" class="btn-search">
                        <div class="result-autocomplete"></div>
                    </form>
                </div>
            </div>
        </div>
        <div class="text-center" id="resolveAddress"></div>
        <div class="forecast-data py-3" id="forecast-data">

        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        var city = '';
        $(document).ready(function() {
            $('#location-search').focus();
        });
        $(document).on('keypress', '#location-search', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault(); // Prevent form submission or page reload
                // $('.loader').show();
                // setTimeout(() => {
                //     handleSearch();
                // }, 500);
            }
        });

        $(document).on('keyup', '#location-search', function(event) {
            event.preventDefault();
            autoComplete($(this).val());

        });

        $(document).on('click', '.searchLocation', function(event) {
            event.preventDefault();
            $('.loader').show();
            $('.result-autocomplete').hide();
            city = $(this).attr('data-id');
            setTimeout(() => {
                handleSearch($(this).attr('data-lat'), $(this).attr('data-long'));
            }, 500);

        });

        function autoComplete(val) {
            fetch('https://geocoding-api.open-meteo.com/v1/search?name=' + val + '&count=10&language=en&format=json')
                .then(response => {

                    return response.json();
                })
                .then(data => {
                    // Process the response data
                    console.log(data);
                    var html = '';
                    if (data.results.length > 0) {
                        data.results.forEach(el => {
                            html +=
                                `<li class="searchLocation my-2" style="cursor:pointer;" data-id="${el.name}" data-lat="${el.latitude}"
                            data-long="${el.longitude}">${el.admin1}, ${el.country}, ${el.country_code} (${el.timezone})</li>`
                        });
                        $('.result-autocomplete').html(`<ul style="list-style: none;padding-left: 11px;">${html}</ul>`)
                            .show();
                    } else {
                        $('.result-autocomplete').html('').hide();
                    }
                })
                .catch(error => {
                    // Handle any errors
                    console.error('Error:', error);
                });

        }

        function handleSearch(lat, long) {
            var form_data = new FormData($('#location-forecast')[0]);
            form_data.append('lat', lat);
            form_data.append('long', long);

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
                                    <div class="card-header border-dark py-3 px-1">
                                        <div class="card-title mb-0"><p class="mb-0">${dayString}<br>${res.days[index]?.datetime}<br>(${res?.timezone})</p></div>
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
                            $('#resolveAddress').html(`<h3 class="mb-0">${city}</h3>`)
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
