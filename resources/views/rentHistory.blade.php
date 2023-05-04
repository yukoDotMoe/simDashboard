@extends('layouts.app')

@section('title')
    Rent History
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Rent History </h2>

    <div class="w-full mb-8 overflow-hidden bg-white rounded-lg shadow-md">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                    <th class="px-4 py-3">Service</th>
                    <th class="px-4 py-3">Price</th>
                    <th class="px-4 py-3">Phone Number</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Content</th>
                    <th class="px-4 py-3">Date</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="rentTable">
                @foreach($data['activities'] as $task)
                    <tr class="text-gray-700 dark:text-gray-400" id="{{ $task['uniqueId'] }}">
                        <td class="px-4 py-3 text-sm" id="serviceName"> {{ $task['serviceName'] }} </td>
                        <td class="px-4 py-3 text-sm" id="servicePrice"> {{ number_format($task['servicePrice'], 0, '', ',') }} </td>
                        <td class="px-4 py-3 text-sm phoneNumber" id="phoneNumber"> {{ $task['phone'] }} </td>
                        <td class="px-4 py-3 text-sm" id="status">
                                @switch($task['status'])
                                    @case(0)
                                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-700">
                                          Timeout
                                        </span>
                                        @break
                                    @case(1)
                                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                            Completed
                                        </span>
                                        @break
                                    @case(2)
                                        <span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                                          Waiting
                                        </span>
                                        @break
                                @endswitch
                        </td>
                        <td class="px-4 py-3 text-sm code" id="code">{{ $task['code'] }}</td>
                        <td class="px-2 py-3 text-sm content" id="content">{{ $task['smsContent'] }}</td>
                        <td class="px-4 py-3 text-sm" id="createdTime"> {{ $task['created_at'] }} </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $data['activities']->links() !!}


        </div>
    </div>
@endsection

@section('js')
    <script>
        $( document ).ready(function() {
            function copyToClipboard(text) {
                var sampleTextarea = document.createElement("textarea");
                document.body.appendChild(sampleTextarea);
                sampleTextarea.value = text; //save main text in it
                sampleTextarea.select(); //select textarea contenrs
                document.execCommand("copy");
                document.body.removeChild(sampleTextarea);
                vt.success(`Copied "${text}" to your clipboard.`, {
                    title: "Text copied",
                    position: "top-right",
                })
            }

            $('.code, .phoneNumber').click(function (){
                const text = $(this).text();
                if(!text) return false
                copyToClipboard(text)
            })
        });
    </script>
@endsection