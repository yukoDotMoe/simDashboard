@extends('layouts.app')

@section('title')
    Tạo Đại Lí
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Tạo đại lí mới </h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <form action="{{ route('admin.vendors.create.post') }}" method="POST">
                @csrf
                <label class="block text-sm mt-2 mb-4">
                    <span class="text-gray-700 dark:text-gray-400">Username</span>
                    <input name="username" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" required>
                </label>

            <div class="relative w-full max-w-xl mr-6 focus-within:text-purple-500" >
                <div class="absolute inset-y-0 flex items-center pl-2">
                    <svg
                            class="w-4 h-4"
                            aria-hidden="true"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                    >
                        <path
                                fill-rule="evenodd"
                                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                clip-rule="evenodd"
                        ></path>
                    </svg>
                </div>
                <div class="relative text-gray-500 focus-within:text-purple-600" >
                    <input class="block w-full pr-20 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Password" name="password" id="searchInput" />
                    <button id="randompass" class="absolute inset-y-0 right-0 px-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-r-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                        Ngẫu nhiên
                    </button>
                </div>

            </div>

                <label class="block text-sm mt-2 mb-4">
                    <span class="text-gray-700 dark:text-gray-400">% Doanh thu</span>
                    <input min="1" value="75" max="100" name="profit" type="number" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" required>
                </label>

                <button id="createService" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Tạo đại lí
                </button>
            </form>
        </p>
    </div>
@endsection

@section('js')
<script>
    $('#randompass').click(function (e) {
        e.preventDefault()

        var length = Math.floor(Math.random() * 18) + 10,
            charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
            retVal = "";
        for (var i = 0, n = charset.length; i < length; ++i) {
            retVal += charset.charAt(Math.floor(Math.random() * n));
        }
        $('input[name="password"]').val(retVal)
    })
</script>
@endsection