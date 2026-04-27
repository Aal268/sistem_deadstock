@php
    $appName = config('app.name', 'Sistem Deadstock');
    $resolvedTitle = isset($pageTitle) && filled($pageTitle)
        ? trim($pageTitle).' - '.$appName
        : $appName;
@endphp

<title>{{ $resolvedTitle }}</title>
<link rel="icon" href="{{ asset('img/fav-ums.png') }}" type="image/webp">
<link rel="shortcut icon" href="{{ asset('img/fav-ums.png') }}" type="image/webp">
