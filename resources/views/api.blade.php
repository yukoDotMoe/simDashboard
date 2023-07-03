@extends('layouts.app')

@section('title')
    API Documents
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Tài liệu API <p class="text-xs text-gray-600 dark:text-gray-200">
            Tích hợp trang web của chúng tôi vào ứng dụng của bạn dễ dàng
        </p></h2>
    <a class="flex items-center justify-between p-4 mb-6 text-sm font-semibold text-purple-100 bg-purple-600 rounded-lg shadow-md focus:outline-none focus:shadow-outline-purple">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"></path>
            </svg>
            <span>Token hiện tại: <strong id="copyToken">{{ Auth::user()->api_token }}</strong> </span>
        </div>
    </a>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        {{ $data['apiDocs']['lastUpdate'] }}
        @if(empty($data['apiDocs']['value']))
            Tài liệu không khả dụng vào thời điểm hiện tại
        @else
            @php
            $array_from_to = array (
                '<ul' => '<ul class="max-w-md space-y-1 text-gray-500 list-disc list-inside dark:text-gray-400"',
                '<ol' => '<ol class="pl-5 mt-2 space-y-1 list-decimal list-inside"',
                '<hr' => '<hr class="my-8"',
                'language-javascript' => 'language-javascript p-3 text-xs italic font-normal text-gray-500 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300'
            );
            @endphp
            {!! str_replace(array_keys($array_from_to), $array_from_to, $data['apiDocs']['value']) !!}
        @endif
    </div>
@endsection