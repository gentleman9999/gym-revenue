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
    {{ csrf_field() }}
        <div class="flex flex-1">
            <div class="flex-1">
              @inertia

            </div>
        </div>

        <footer class="bg-base-200 p-2 text-base-content opacity-60 w-full">

            <div class="text-muted ml-auto mr-auto text-center">
                <div class="inner-footer">
                    <small><b>CONCEPTUAL PROTOTYPE</b> | v.<b>{!! env('APP_VERSION') !!}</b> | Build <b>{!! env('APP_BUILD') !!}</b></small> | <b><small>© 2022 GymRevenue.</small></b>
                    <br />
                    <b><small>GymRevenue is a Registered Trademark of <a href="https://capeandbay.com" target="_blank">Cape & Bay, LLC</a>. All Rights Reserved. </small></b>
                </div>
            </div>
        </footer>
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
