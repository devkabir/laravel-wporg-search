<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100 ">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>

<body class="w-full px-4 py-2 font-sans antialiased text-gray-900">
    @livewire('search-form')
    @livewireScripts
</body>

</html>
