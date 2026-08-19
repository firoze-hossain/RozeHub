<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? config('app.name', 'RozeHub') }}</title>
<link rel="stylesheet" href="{{ asset('css/rozehub.css') }}">
@livewireStyles
</head>
<body>{{ $slot }}@livewireScripts</body>
</html>
