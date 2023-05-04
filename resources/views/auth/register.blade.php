@extends('layouts.auth')

@section('content')
<h1 class="mb-4 text-xl font-semibold text-gray-700 dark:text-gray-200"> Register </h1>
<x-auth-validation-errors class="mb-4" :errors="$errors" />
<form method="POST" action="{{ route('register') }}">
    @csrf
    <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Name</span>
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="Name" name="name" type="text" required />
    </label>
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Email</span>
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="Email" type="email" name="email" required />
    </label>
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Password</span>
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="Password" type="password" name="password" required autocomplete="new-password" />
    </label>
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Confirm Password</span>
        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" placeholder="Password" type="password" name="password_confirmation" required/>
    </label>
    <button type="submit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"> Sign Up </button>
</form>
<p class="mt-1">
    <a class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline" href="{{ route('login') }}"> Already registered? </a>
</p>
@endsection