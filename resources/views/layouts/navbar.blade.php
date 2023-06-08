<!-- Desktop sidebar -->
<aside class="z-20 hidden w-64 overflow-y-auto bg-white dark:bg-gray-800 md:block flex-shrink-0">
    <div class="py-4 text-gray-500 dark:text-gray-400">
        <a class="ml-6 flex  text-lg font-bold text-gray-800 dark:text-gray-200" href="#">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1117.25 10.5"></path>
            </svg>
            {{ config('app.name', 'Laravel') }}
        </a>
        <div class="mt-6">
        </div>
        <ul>
            @if(Auth::user()->tier < 10)
                @foreach(config('simConfig.navBar') as $nav => $link)
                    @if(isset($link['separator']))
                        <p class="relative px-6 py-3 text-sm font-medium text-gray-600 dark:text-gray-400"> {{ $link['name'] }} </p>
                    @else
                        @if($link['active'])
                            @if(!$link['dropdown'])
                                <li class="relative px-6 py-3">
                                    @if(request()->segment(1) == $link['redirect'])
                                        <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                                    @endif
                                    <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 @if(request()->segment(1) == $link['redirect']) text-gray-800 dark:text-white dark:hover:text-gray-200 @else hover:text-gray-800 dark:hover:text-gray-200 @endif " href="{{ ($link['singleLink']) ? $link['redirect'] : route($link['redirect']) }}">
                                        {!!
                                            str_replace([
                                                'stroke-width="1.5"',
                                                'svg'
                                            ],[
                                                'stroke-width="2"',
                                                'svg class="w-5 h-5"'
                                            ], $link['icon'])
                                        !!}
                                        <span class="ml-4">{{ $link['name'] }}</span>
                                    </a>
                                </li>
                            @else
                                <li class="relative px-6 py-3">
                                    <button class="inline-flex items-center justify-between w-full text-sm font-semibold transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200" @click="togglePagesMenu" aria-haspopup="true">
                                <span class="inline-flex items-center">
                                  {!!
                                  str_replace([
                                    'stroke-width="1.5"',
                                    'svg'
                                  ],[
                                    'stroke-width="2"',
                                    'svg class="w-5 h-5"'
                                  ], $link['icon'])
                                  !!}
                                  <span class="ml-4">{{ $link['name'] }}</span>
                                </span>

                                        <svg class="w-4 h-4" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <template x-if="isPagesMenuOpen">
                                        <ul x-transition:enter="transition-all ease-in-out duration-300" x-transition:enter-start="opacity-25 max-h-0" x-transition:enter-end="opacity-100 max-h-xl" x-transition:leave="transition-all ease-in-out duration-300" x-transition:leave-start="opacity-100 max-h-xl" x-transition:leave-end="opacity-0 max-h-0" class="p-2 mt-2 space-y-2 overflow-hidden text-sm font-medium text-gray-500 rounded-md shadow-inner bg-gray-50 dark:text-gray-400 dark:bg-gray-900" aria-label="submenu">
                                            @foreach($link['dropdown'] as $dropdown)
                                                @if(request()->segment(2) == $dropdown['link'])
                                                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                                                @endif
                                                <li class="px-2 py-1 transition-colors duration-150 @if(request()->segment(1) == $link['redirect']) text-gray-800 dark:text-white dark:hover:text-gray-200 @else hover:text-gray-800 dark:hover:text-gray-200 @endif">
                                                    <a class="w-full" href="{{ $dropdown['link'] }}">{{ $dropdown['name'] }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </template>
                                </li>
                            @endif
                        @endif
                    @endif
                @endforeach

                @if(Auth::user()->admin)
                    <hr class="my-2 mx-3">
                    <p class="relative px-6 py-3 text-sm font-medium text-gray-600 dark:text-gray-400"> Dụng cụ Admin </p>
                    @foreach(config('simConfig.adminNavbar') as $nav => $link)
                        @if($link['active'])
                            <li class="relative px-6 py-3">
                                @if(request()->segment(2) == $nav)
                                    <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                                @endif
                                <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 @if(request()->segment(2) == $nav) text-gray-800 dark:text-white dark:hover:text-gray-200 @else hover:text-gray-800 dark:hover:text-gray-200 @endif" href="{{ ($link['singleLink']) ? $link['redirect'] : route($link['redirect']) }}">
                                    {!!
                                        str_replace([
                                            'stroke-width="1.5"',
                                            'svg'
                                        ],[
                                            'stroke-width="2"',
                                            'svg class="w-5 h-5"'
                                        ], $link['icon'])
                                    !!}
                                    <span class="ml-4">{{ $link['name'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @else
                @foreach(config('simConfig.vendorNavbar') as $nav => $link)
                    @if($link['active'])
                        <li class="relative px-6 py-3">
                            @if(request()->segment(2) == $nav)
                                <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg" aria-hidden="true"></span>
                            @endif
                            <a class="inline-flex items-center w-full text-sm font-semibold transition-colors duration-150 @if(request()->segment(2) == $nav) text-gray-800 dark:text-white dark:hover:text-gray-200 @else hover:text-gray-800 dark:hover:text-gray-200 @endif" href="{{ ($link['singleLink']) ? $link['redirect'] : route($link['redirect']) }}">
                                {!!
                                    str_replace([
                                        'stroke-width="1.5"',
                                        'svg'
                                    ],[
                                        'stroke-width="2"',
                                        'svg class="w-5 h-5"'
                                    ], $link['icon'])
                                !!}
                                <span class="ml-4">{{ $link['name'] }}</span>
                            </a>
                        </li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div>
</aside>