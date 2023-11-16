
<?php

$manifest = json_decode(file_get_contents(ABSOLUTE_APP_PATH . '/public/manifest.json'), true);

$cssFile = $manifest['resources/js/main.jsx']['file'];
$jsFile = $manifest['resources/js/main.jsx']['file'];

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'React & Laravel') }}</title>

        <!-- Styles -->
        <link href="{{ asset($cssFile) }}" rel="stylesheet">

        <!-- Scripts -->
        <script src="{{ asset($jsFile) }}" defer></script>
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
