<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="gr-dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php $asset_name = ((date('H') >= 23) || (date('H') < 16 )) ? '/img/gr-dark-logo.png' : '/img/gr-light-logo.png' @endphp
        <link rel="icon" type="image/png" href="{{ asset($asset_name) }}">

        <title inertia>{{ env('APP_NAME', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
        <link rel="stylesheet" href="{{ mix('sass/app.css') }}">

        <!-- Scripts -->
        @routes
        <script src="{{ mix('js/app.js') }}" defer></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        @inertia

        <footer class="bg-base-200 p-4 text-gray-400">
            <div class="text-muted ml-auto mr-auto text-center">
                <div class="inner-footer">
                    <b>
                    <small><i class="fal fa-copyright"></i>2021. Cape & Bay. All Rights Reserved. </small></b>
                    <br />
                    <small><b>CONCEPTUAL PROTOTYPE</b> | v.<b>{!! env('APP_VERSION') !!}</b>| Build <b>{!! env('APP_BUILD') !!}</b></small>
                </div>
            </div>
        </footer>
        @env ('local')
            <!-- <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script> -->
        @endenv
    </body>
</html>
