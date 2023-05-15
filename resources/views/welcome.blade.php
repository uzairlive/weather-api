<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Weather Forecast</title>

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
    </style>
</head>

<body class="antialiased">
    <div class="loader">
        <img src="/images/rain.gif" alt="Loader">
    </div>
    <div class="container">
        <header class="header">
            <h2>Weather Forecast</h2>
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



</body>

</html>
