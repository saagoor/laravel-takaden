<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @isset($title)
            <title>{{ $title }}</title>
        @else
            <title>{{ config('app.name', 'Laravel') }}</title>
        @endisset
        @stack('head')
        @isset($head)
            {{ $head }}
        @endisset
    </head>
    <body class="antialiased bg-gray-100">
        <div class="max-w-full mx-auto prose-sm prose">
            {{ $slot }}
        </div>
        @stack('footer')
        @isset($footer)
            {{ $footer }}
        @endisset
    </body>
</html>
