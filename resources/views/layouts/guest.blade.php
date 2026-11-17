<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Food-Sama: administra tu menú, pedidos, comprobantes y ganancias desde un solo lugar.">
        <title>{{ $title ?? 'Food-Sama' }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#fbfaf7] text-neutral-900 antialiased">
        {{ $slot }}
    </body>
</html>
