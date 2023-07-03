<!DOCTYPE html>
<html lang="en" class="astro-FLTEP2YP">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <meta name="description" content="Rent temporary sim cards for mobile connectivity without long-term commitment or high costs. Get connected easily and affordably.">
    <title>{{ config('app.name', 'Laravel') }} - Sàn cung cấp sim đáng tin cậy</title>

    <link href="https://fonts.googleapis.com/css2?family=Urbanist:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/index.350e2433.css"/>
</head>
<body class="bg-white dark:bg-gray-900 astro-FLTEP2YP">
<header class="astro-UY3JLCBK">
    <nav class="z-10 w-full absolute astro-UY3JLCBK">
        <div class="max-w-7xl mx-auto px-6 md:px-12 xl:px-6">
            <div class="flex flex-wrap items-center justify-between py-2 gap-6 md:py-4 md:gap-0 relative astro-UY3JLCBK">
                <input aria-hidden="true" type="checkbox" name="toggle_nav" id="toggle_nav"
                       class="hidden peer astro-UY3JLCBK">
                <div class="relative z-20 w-full flex justify-between lg:w-max md:px-0 astro-UY3JLCBK">
                    <a href="#" aria-label="logo" class="flex space-x-2 items-center astro-UY3JLCBK">
                        <span class="text-2xl font-bold text-gray-900 dark:text-white astro-UY3JLCBK">{{ config('app.name', 'Laravel') }}</span>
                    </a>

                    <div class="relative flex items-center lg:hidden max-h-10 astro-UY3JLCBK">
                        <label role="button" for="toggle_nav" aria-label="humburger" id="hamburger"
                               class="relative  p-6 -mr-6 astro-UY3JLCBK">
                            <div aria-hidden="true" id="line"
                                 class="m-auto h-0.5 w-5 rounded bg-sky-900 dark:bg-gray-300 transition duration-300 astro-UY3JLCBK"></div>
                            <div aria-hidden="true" id="line2"
                                 class="m-auto mt-2 h-0.5 w-5 rounded bg-sky-900 dark:bg-gray-300 transition duration-300 astro-UY3JLCBK"></div>
                        </label>
                    </div>
                </div>
                <div aria-hidden="true"
                     class="fixed z-10 inset-0 h-screen w-screen bg-white/70 backdrop-blur-2xl origin-bottom scale-y-0 transition duration-500 peer-checked:origin-top peer-checked:scale-y-100 lg:hidden dark:bg-gray-900/70 astro-UY3JLCBK"></div>
                <div class="flex-col z-20 flex-wrap gap-6 p-8 rounded-3xl border border-gray-100 bg-white shadow-2xl shadow-gray-600/10 justify-end w-full invisible opacity-0 translate-y-1  absolute top-full left-0 transition-all duration-300 scale-95 origin-top
                            lg:relative lg:scale-100 lg:peer-checked:translate-y-0 lg:translate-y-0 lg:flex lg:flex-row lg:items-center lg:gap-0 lg:p-0 lg:bg-transparent lg:w-7/12 lg:visible lg:opacity-100 lg:border-none
                            peer-checked:scale-100 peer-checked:opacity-100 peer-checked:visible lg:shadow-none
                            dark:shadow-none dark:bg-gray-800 dark:border-gray-700 astro-UY3JLCBK">

                    @if (Route::has('login'))
                        <div class="text-gray-600 dark:text-gray-300 lg:pr-4 lg:w-auto w-full lg:pt-0 astro-UY3JLCBK">
                            <ul class="tracking-wide font-medium lg:text-sm flex-col flex lg:flex-row gap-6 lg:gap-0 astro-UY3JLCBK">
                                @auth
                                @else
                                    <li class="astro-UY3JLCBK">
                                        <a href="{{ route('login') }}"
                                           class="block md:px-4 transition hover:text-primary astro-UY3JLCBK">
                                            <span class="astro-UY3JLCBK">Đăng nhập</span>
                                        </a>
                                    </li>
                                @endauth
                            </ul>
                        </div>

                        @auth
                            <div class="mt-12 lg:mt-0 astro-UY3JLCBK">
                                <a href="{{ url('/dashboard') }}"
                                   class="relative flex h-9 w-full items-center justify-center px-4 before:absolute before:inset-0 before:rounded-full before:bg-primary before:transition before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95 sm:w-max astro-UY3JLCBK">
                                    <span class="relative text-sm font-semibold text-white astro-UY3JLCBK">Trang chủ</span>
                                </a>
                            </div>
                        @else
                            @if (Route::has('register'))
                                <div class="mt-12 lg:mt-0 astro-UY3JLCBK">
                                    <a href="{{ route('register') }}"
                                       class="relative flex h-9 w-full items-center justify-center px-4 before:absolute before:inset-0 before:rounded-full before:bg-primary before:transition before:duration-300 hover:before:scale-105 active:duration-75 active:before:scale-95 sm:w-max astro-UY3JLCBK">
                                        <span class="relative text-sm font-semibold text-white astro-UY3JLCBK">Đăng kí</span>
                                    </a>
                                </div>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>
</header>
<main class="space-y-40 mb-10">
    <div class="relative">
        <div aria-hidden="true" class="absolute inset-0 grid grid-cols-2 -space-x-52 opacity-40 dark:opacity-20">
            <div class="blur-[106px] h-56 bg-gradient-to-br from-primary to-purple-400 dark:from-blue-700"></div>
            <div class="blur-[106px] h-32 bg-gradient-to-r from-cyan-400 to-sky-300 dark:to-indigo-600"></div>
        </div>
        <div class="max-w-7xl mx-auto px-6 md:px-12 xl:px-6">
            <div class="relative pt-36 ml-auto">
                <div class="lg:w-2/3 text-center mx-auto">
                    <h1 class="text-gray-900 dark:text-white font-bold text-5xl md:text-6xl xl:text-7xl">Get Connected
                        Anywhere, <span class="text-primary dark:text-white">Anytime.</span></h1>
                    <p class="mt-8 text-gray-700 dark:text-gray-300">Connect temporarily with ease and affordability
                        through our platform, offering hassle-free, tailored options for your mobile connectivity needs.
                        Stay connected on the go, anytime, anywhere.</p>

                    <div class="hidden py-8 mt-16 border-y border-gray-100 dark:border-gray-800 sm:flex justify-between">
                        <div class="text-left">
                            <h6 class="text-lg font-semibold text-gray-700 dark:text-white">The lowest price</h6>
                            <p class="mt-2 text-gray-500">Quality sim card rentals at the lowest prices available.</p>
                        </div>
                        <div class="text-left">
                            <h6 class="text-lg font-semibold text-gray-700 dark:text-white">The fastest on the
                                market</h6>
                            <p class="mt-2 text-gray-500">Lightning-fast sim card rentals: Get connected in minutes!</p>
                        </div>
                        <div class="text-left">
                            <h6 class="text-lg font-semibold text-gray-700 dark:text-white">The most reliable</h6>
                            <p class="mt-2 text-gray-500"> Stay connected without interruptions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<footer class="flex items-center justify-center mt-6">
    <span class="block text-gray-500 dark:text-gray-400">@ {{ now()->year }}. Powered by <a href="#" class="font-semibold text-gray-600 dark:text-white">{{ config('app.name', 'Laravel') }}</a></span>

</footer>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.documentElement.classList.remove('dark')
    });
</script>
</body>
</html>