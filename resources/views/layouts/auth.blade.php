<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="./assets/css/tailwind.output.css" />
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="./assets/js/init-alpine.js"></script>
</head>
<body>
<div class="flex items-center min-h-screen p-6 bg-gray-50 dark:bg-gray-900">
    <div class="flex-1 h-full max-w-xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
        <div class="flex overflow-y-auto md:flex-row">
            <div class="items-center justify-center p-6 pb-0 sm:p-12 w-full">
                <div class="w-full">
                    @yield('content')
                </div>
            </div>
        </div>
        <div class="flex items-center justify-center py-4">
        <small class="text-gray-400 text-center">@ {{ now()->year }}. Powered by {{ config('app.name', 'Laravel') }}</small>
        </div>
    </div>
</div>
<script src="{{ asset('/assets/js/vanilla-toast.min.js') }}" type="text/javascript"></script>
<script>
    @if(Session::has('success'))
    vt.success('{{ session('success') }}', {
        title: "Success",
        position: "top-right",
    })
    @endif

    @if(Session::has('error'))
    vt.error('{{ session('error') }}', {
        title: "Error",
        position: "top-right",
    })
    @endif
</script>
</body>
</html>