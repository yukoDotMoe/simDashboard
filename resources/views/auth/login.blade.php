@extends('layouts.auth')

@section('content')
<h1 class="mb-4 text-xl font-semibold text-gray-700 dark:text-gray-200"> Đăng nhập </h1>
<x-auth-session-status class="mb-4" :status="session('status')" />
<x-auth-validation-errors class="mb-4" :errors="$errors" />
<form method="POST" action="{{ route('login') }}">
    @csrf
    <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Tên đăng nhập</span>
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="Username" name="username" type="text" required autofocus />
    </label>
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Mật khẩu</span>
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="Password" type="password" name="password" required autocomplete="current-password" />
    </label>
    <button type="submit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"> Đăng nhập </button>
</form>
@if (Route::has('password.request'))
<p class="mt-4">
    <a class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline" href="{{ route('password.request') }}"> Forgot your password? </a>
</p>
@endif
@if (Route::has('register'))
<p class="mt-1">
    <a class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline" href="{{ route('register') }}"> Tạo tài khoản </a>
</p>
@endif
@endsection