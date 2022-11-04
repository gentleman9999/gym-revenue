<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @php $asset_name = ((date('H') >= 23) || (date('H') < 16 )) ? '/img/gr-dark-logo.png' : '/img/gr-light-logo.png' @endphp
        <link rel="icon" type="image/png" href="{{ asset($asset_name) }}">

        <title inertia>{{ env('APP_NAME', 'Laravel') }}</title>

        <!-- Scripts -->
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js'])
{{--        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>--}}
    </head>
    <body class="font-sans antialiased flex flex-col min-h-screen">
        <div class="flex flex-1">
            <div class="flex-1">
              @inertia

            </div>
        </div>


        @env ('local')
            <!-- <script src="http://localhost:3000/browser-sync/browser-sync-client.js"></script> -->
        @endenv
    </body>
    <script>
        let theme = localStorage.getItem("theme")
        if (theme) {
            document.getElementsByTagName("html")[0].dataset.theme=theme;
        }
    </script>
</html>
