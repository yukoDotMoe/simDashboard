@extends('layouts.app')

@section('title')
    Vendor's Dashboard
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Sim List <p class="text-xs text-gray-600 dark:text-gray-200">
            Browse, compare, and select Sims for rent. Build your virtual world today.
        </p></h2>
    <div
            x-show="isModalOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-30 flex items-end bg-black bg-opacity-50 sm:items-center sm:justify-center"
    >
        <!-- Modal -->
        <div
                x-show="isModalOpen"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 transform translate-y-1/2"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0  transform translate-y-1/2"
                @click.away="closeModal"
                @keydown.escape="closeModal"
                class="w-full px-6 py-4 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl hidden"
                role="dialog"
                id="modal"
        >
            <!-- Modal body -->
            <div class="mt-4 mb-6">
                <!-- Modal title -->
                <p class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300" >
                    Now showing activities of SIM: <span id="userId"></span>
                </p>
                <label class="block mt-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">
                      <strong>Succeed Requests</strong>
                    </span>
                    <div class="w-full mb-8 overflow-hidden bg-white rounded-lg shadow-md mt-2">
                        <div class="w-full overflow-x-auto">
                            <table class="w-full whitespace-no-wrap">
                                <thead>
                                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                    <th class="px-4 py-3">Request ID</th>
                                    <th class="px-4 py-3">Service</th>
                                    <th class="px-4 py-3">Date</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="successList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </label>

                <hr>

                <label class="block mt-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">
                      <strong>Failed Requests</strong>
                    </span>
                    <div class="w-full mb-8 overflow-hidden bg-white rounded-lg shadow-md mt-2">
                        <div class="w-full overflow-x-auto">
                            <table class="w-full whitespace-no-wrap">
                                <thead>
                                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                    <th class="px-4 py-3">Request ID</th>
                                    <th class="px-4 py-3">Service</th>
                                    <th class="px-4 py-3">Reason</th>
                                    <th class="px-4 py-3">Date</th>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="failedList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </label>
            </div>
            <footer
                    class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800"
            >
                <button
                        @click="closeModal"
                        class="w-full px-5 py-3 text-sm font-medium leading-5 text-white text-gray-700 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 sm:px-4 sm:py-2 sm:w-auto active:bg-transparent hover:border-gray-500 focus:border-gray-500 active:text-gray-500 focus:outline-none focus:shadow-outline-gray"
                >
                    Huỷ
                </button>

            </footer>
        </div>
    </div>
    <div class="grid gap-6 mb-4 md:grid-cols-3 xl:grid-cols-3">
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Total Sims</p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalProfit">{{ $data['online'] + $data['offline'] }}</p>
            </div>
        </div>
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Online</p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalProfit">{{ $data['online'] }}</p>
            </div>
        </div>

        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Offline</p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalRequest">{{ $data['offline'] }}</p>
            </div>
        </div>

    </div>
    <div class="w-full mb-8 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <div id="sims" class="text-center"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            let dataList = [
                @foreach($data['sims'] as $task)
                    ["{{ $task['phone'] }}", "@switch($task['status']) @case(0) Offline @break @case(1) Online @break @case(2) Working @break @endswitch", "{{ $task['success'] }}", "{{ $task['failed'] }}", "{{ $task['date'] }}", gridjs.html(`
                    <div class="flex items-center space-x-4 text-sm">
                                <button @click="openModal" data-sim="{{ $task['id'] }}" class="historyBtn flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Edit">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </button>
                            </div>
                    `),],
                @endforeach
            ];

            $('#sims').html('')
            new gridjs.Grid({
                columns: ["Phone", "Status", "Success", "Failed", "Last Update", "Actions"],
                data: dataList,
                search: true,
                sort: {
                    multiColumn: false
                },
                pagination: true
            }).render(document.getElementById("sims"));
            $('#modal').removeClass('hidden')
            console.log( "\u001b[1;31m You shouldn't open this panel you know. But welcome, {{ Auth::user()->email }}" );
        })

        $(document).on('click','.historyBtn',function(e){
            e.preventDefault()
            const simId = $(this).attr('data-sim');

            $.ajax({
                type: "POST",
                url: "{{ route('vendor.sims') }}/"+simId,
                cache: false,
                success: function (data) {
                    data= data.data
                    $('#userId').text(data.phone)
                    successList = data.success
                    $('#successList').html('')
                    console.log(typeof successList)
                    if(Array.isArray(successList) && Object.keys(successList).length > 0)
                    {
                        Object.keys(successList).forEach(function(key) {
                            $('#successList').append(`
                        <tr class="text-gray-700 dark:text-gray-400 locked_item" data-id="${key}">
                            <td class="px-4 py-3 text-sm">${successList[key]['request'] ?? "⁉"}</td>
                            <td class="px-4 py-3 text-sm">${successList[key]['service'] ?? "⁉"}</td>
                            <td class="px-4 py-3 text-sm">${successList[key]['date'] ?? "⁉"}</td>
                        </tr>
                    `)
                        });
                    }

                    failedList = data.failed
                    $('#failedList').html('')
                    if(Array.isArray(failedList) && Object.keys(failedList).length > 0)
                    {
                        Object.keys(failedList).forEach(function(key) {
                            $('#failedList').append(`
                        <tr class="text-gray-700 dark:text-gray-400 locked_item" data-id="${key}">
                            <td class="px-4 py-3 text-sm">${failedList[key]['request'] ?? "⁉"}</td>
                            <td class="px-4 py-3 text-sm">${failedList[key]['service'] ?? "⁉"}</td>
                            <td class="px-4 py-3 text-sm">${failedList[key]['reason'] ?? "⁉"}</td>
                            <td class="px-4 py-3 text-sm">${failedList[key]['date'] ?? "⁉"}</td>
                        </tr>
                    `)
                        });
                    }
                },
                error: function (e) {
                }
            });
        })
    </script>
@endsection