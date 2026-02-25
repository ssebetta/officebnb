<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- SEO Meta Tags -->
        <meta name="description" content="OfficeBnB - Find and book flexible office spaces by the day, week, or month. Premium workspaces for teams that move fast.">
        <meta name="keywords" content="office space, office rental, flexible workspace, coworking, day office, weekly office, monthly office">
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        <meta name="author" content="OfficeBnB">
        <meta name="language" content="English">
        <meta name="revisit-after" content="7 days">
        <meta name="copyright" content="OfficeBnB &copy; 2026">

        <!-- Favicons -->
        <link rel="icon" href="/web/favicon.ico" sizes="any">
        <link rel="icon" href="/web/icon-512.png" type="image/png">
        <link rel="apple-touch-icon" href="/web/apple-touch-icon.png">
        <link rel="manifest" href="/manifest.json">

        <!-- Open Graph / Social Media -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ config('app.url') }}">
        <meta property="og:title" content="OfficeBnB - Professional Office Spaces On Demand">
        <meta property="og:description" content="Find and book flexible office spaces by the day, week, or month. Premium workspaces for teams that move fast.">
        <meta property="og:image" content="{{ config('app.url') }}/web/icon-512.png">
        <meta property="og:site_name" content="OfficeBnB">
        <meta property="og:locale" content="en_US">

        <!-- Twitter Card -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="{{ config('app.url') }}">
        <meta property="twitter:title" content="OfficeBnB - Professional Office Spaces On Demand">
        <meta property="twitter:description" content="Find and book flexible office spaces by the day, week, or month. Premium workspaces for teams that move fast.">
        <meta property="twitter:image" content="{{ config('app.url') }}/web/icon-512.png">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ config('app.url') }}">

        <!-- Sitemap -->
        <link rel="sitemap" type="application/xml" href="{{ route('sitemap.index') }}">

        <!-- Mobile Web App -->
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="theme-color" content="#0F1B2D">

        <title inertia>{{ config('app.name', 'OfficeBnB') }} | Premium Office Spaces On Demand</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
