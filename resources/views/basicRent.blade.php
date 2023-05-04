@extends('layouts.app')

@section('title')
    Basic Rent
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Basic Rent </h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400"> Select Service </span>
                <select id="serviceSelect" class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                @if(count($data['services']) == 0)
                    <option selected disabled>No services found on our server.</option>
                @else
                    @foreach($data['services'] as $service)
                        <option value="{{ $service['uniqueId'] }}" data-name="{{ $service['serviceName'] }}" data-price="{{ number_format($service['price'],0,'',',') }}">{{ $service['serviceName'] }} ({{ number_format($service['price'],0,'',',') }} VND)</option>
                    @endforeach
                @endif
                </select>
            </label>
            <button id="rentSubmit" type="submit" class="block w-full @if(count($data['services']) == 0)
            px-4 py-2 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg opacity-50 cursor-not-allowed focus:outline-none
@else
px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple
@endif"> Rent </button>
        </p>
    </div>

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
                    @foreach($data['workingTask'] as $task)
                        <tr class="text-gray-700 dark:text-gray-400" id="{{ $task['uniqueId'] }}">
                            <td class="px-4 py-3 text-sm" id="serviceName"> {{ $task['serviceName'] }} </td>
                            <td class="px-4 py-3 text-sm" id="servicePrice"> {{ number_format($task['servicePrice'], 0, '', ',') }} </td>
                            <td class="px-4 py-3 text-sm phoneNumber" id="phoneNumber"> {{ $task['phone'] }} </td>
                            <td class="px-4 py-3 text-sm" id="status">
                                <span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                                  Waiting
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm code" id="code"></td>
                            <td class="px-2 py-3 text-sm content" id="content"> </td>
                            <td class="px-4 py-3 text-sm" id="createdTime"> {{ $task['created_at'] }} </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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

            function addToTable(id, name, price, phone, time)
            {
                const table = $('#rentTable');
                const template = `
                <tr class="text-gray-700 dark:text-gray-400" id="${id}">
                        <td class="px-4 py-3 text-sm" id="serviceName"> ${name} </td>
                        <td class="px-4 py-3 text-sm" id="servicePrice"> ${price} </td>
                        <td class="px-4 py-3 text-sm phoneNumber" id="phoneNumber"> ${phone} </td>
                        <td class="px-4 py-3 text-sm" id="status">${waitingBadge}</td>
                        <td class="px-4 py-3 text-sm code" id="code"> </td>
                        <td class="px-2 py-3 text-sm content" id="content"> </td>
                        <td class="px-4 py-3 text-sm" id="createdTime"> ${time} </td>
                </tr>
                `;

                table.prepend(template);
            }

            var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
                cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
            });

            var channel = pusher.subscribe('user-flow.{{ Auth::user()->id }}');
            channel.bind('simUpdateNotify', function(data) {
                const tableRow = $(`tr[id="${data.uniqueId}"]`);
                tableRow.children('#status').html(successBadge);
                tableRow.children('#code').text(data.code)
                tableRow.children('#content').text(data.content)
            });

            channel.bind('simFailed', function(data) {
                const tableRow = $(`tr[id="${data.uniqueId}"]`);
                tableRow.children('#status').html(failedBadge);
                return vt.error("Due to timeout or exception, your requested sim has been canceled", {
                    title: "Sim failed to finish task",
                    position: "top-right",
                })
            });

            $('#rentSubmit').click(function () {
                const selection = $('#serviceSelect').val();

                if(!selection || selection == null)
                {
                    return vt.error("No options available from our site", {
                        title: "Error",
                        position: "top-right",
                    })
                }

                const option = $('option[value="' + selection + '"]');
                const selectionName = option.attr('data-name');
                const selectionPrice = option.attr('data-price');

                $.ajax({
                    url: `{{ route('rentFunc') }}?service=${selection}`,
                    type: "POST",
                    cache: false,
                    success: function (data) {
                        if (data.status != 200)
                        {
                            return vt.error(data.message, {
                                title: "Error",
                                position: "top-right",
                            })
                        }
                        data = data.data
                        vt.success(`Your rent request has been sent.`, {
                            title: "Request Created",
                            position: "top-right",
                        })
                        addToTable(data.requestId, selectionName, selectionPrice, data.phone, data.createdTime)
                    },
                    error: function (e) {
                    }
                });

            })
        });
    </script>
@endsection