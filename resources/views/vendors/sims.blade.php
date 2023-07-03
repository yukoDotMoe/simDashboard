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
                class="w-full px-4 py-2 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl hidden"
                role="dialog"
                id="modal"
        >
            <!-- Modal body -->
            <div class="mt-4 mb-6">
                <!-- Modal title -->
                <p class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300" >
                    Now showing activities of SIM: <span id="userId"></span>
                </p>
                
                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-tab" data-tabs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Current</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="dashboard-tab" data-tabs-target="#dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Successed</button>
        </li>
        <li class="mr-2" role="presentation">
            <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="settings-tab" data-tabs-target="#settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Failed</button>
        </li>
    </ul>
</div>
<div id="myTabContent">
    <div class="hidden rounded-lg" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <label class="block mt-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">
                    <div class="w-full mb-8 overflow-hidden mt-2">
                        <div class="w-full overflow-x-auto">
                            <div id="current"></div>
                        </div>
                    </div>
                </label>

    </div>
    <div class="hidden rounded-lg" id="dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                        <label class="block mt-4 text-sm">
                    <div class="w-full mb-8 overflow-hidden">
                        <div class="w-full overflow-x-auto">
                            <div id="success"></div>
                        </div>
                    </div>
                </label>
    </div>
    <div class="hidden rounded-lg" id="settings" role="tabpanel" aria-labelledby="settings-tab">
                <label class="block mt-4 text-sm">
                    <div class="w-full mb-8 overflow-hidden mt-2">
                        <div class="w-full overflow-x-auto">
                            <div id="failed"></div>
                        </div>
                    </div>
                </label>
    </div>
</div>
                


                <!--<hr>-->


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
        <div class="flex items-center mb-4">
            <input id="checkbox-2" type="checkbox" value="" class="border-2 p-3 rounded-lg w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
            <label for="checkbox-2" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Auto refresh
            </label>
        </div>
        @if (request()->route('showOffline') == 1)
        <a href="{{ route('vendor.sims', ['showOffline' => false]) }}" class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                      Show only online sims
        </a>
        @else
        <a href="{{ route('vendor.sims', ['showOffline' => true]) }}" class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                  Include offline sims
        </a>
        @endif
        <div class="w-full overflow-x-auto">
            <span class="text-xs text-red-600 dark:text-red-400">* Sim's profit only calculate between start and the end of the day.</span>
            <div id="sims" class="text-center"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function getCookie(name) {
            var nameEQ = name + "=";
            var ca = document.cookie.split(';');
            for(var i=0;i < ca.length;i++) {
                var c = ca[i];
                while (c.charAt(0)==' ') c = c.substring(1,c.length);
                if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
            }
            return null;
        }
        function setCookie(name, value, days)
        {
          if (days)
          {
            var date = new Date();
            date.setTime(date.getTime()+days*24*60*60*1000); // ) removed
            var expires = "; expires=" + date.toGMTString(); // + added
          }
          else
            var expires = "";
          document.cookie = name+"=" + value+expires + ";path=/"; // + and " added
        }
        function eraseCookie(name) {   
            document.cookie = name+'=; Max-Age=-99999999;';  
        }
        function hideOrNot(val)
        {
            
            if(val == 0)
            {
                $('#checkbox-2').prop('checked', false)
                eraseCookie('hideHistories')
                setCookie('hideHistories', 0, 7)
            }else{
                $("head").append(`<meta http-equiv="refresh" content="15">`);
                $('#checkbox-2').prop('checked', true)
                eraseCookie('hideHistories')
                setCookie('hideHistories', 1, 7)
            }
        }
        $(document).ready(function () {
            
            $('#checkbox-2').change(function(e){
                if($(this).is(':checked'))
                {
                    hideOrNot(1)
                }else{
                    hideOrNot(0)
                    location.reload()
                }
            })

            var hideHtr = getCookie("hideHistories");
        
            if (hideHtr == null) {
                document.cookie = "hideHistories=1;";
                hideOrNot(1)
            }
            else {
                cookie = getCookie('hideHistories')
                console.log(cookie)
                if(cookie == null) cookie = 1
                hideOrNot(cookie)
            }
            
            let dataList = [
                @foreach($data['sims'] as $task)
                    ["{{ $task['id'] }}","{{ $task['phone'] }}", "{{ $task['network'] }}", "@switch($task['status']) @case(0) Offline @break @case(1) Online @break @case(2) Working @break @endswitch", "{{ $task['success'] }}", "{{ $task['failed'] }}", "{{ number_format($task['totalProfit'], 0, '', ',') }}", "{{ $task['date'] }}", gridjs.html(`
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
                columns: ["ID", "Phone", "Network", "Status", {
                    name: "Success",
                    width: "10%"
                }, "Failed", "Profit", {
                    name: "Created At",
                    width: "12%"
                }, "Actions"],
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
        
        let first = true;
        let current;
        let success;
        let fail;

        $(document).on('click','.historyBtn',function(e){
            e.preventDefault()
            const simId = $(this).attr('data-sim');
            
            $('button[data-tabs-target="#profile"]').click();

            $.ajax({
                type: "POST",
                url: "{{ route('vendor.sims') }}/"+simId,
                cache: false,
                success: function (data) {
                    data = data.data
                    
                    $('#userId').text(data.phone)
                    
                    if (first)
                    {
                        current = new gridjs.Grid({
                            columns: [{
                                name: "ID",
                                id: "request"
                            }, "User", "Service", "Date"],
                            data: data.working,
                        }).render(document.getElementById("current"));
                        
                        success = new gridjs.Grid({
                            columns: [{
                                name: "ID",
                                id: "request"
                            }, "Service", "User", "Price", "Date"],
                            data: data.success,
                            // pagination: {
                            //     limit: 3,
                            //     summary: false
                            // }
                        }).render(document.getElementById("success"));
                        
                        fail = new gridjs.Grid({
                            columns: [{
                                name: "ID",
                                id: "request"
                            }, "Service", "User", "Reason", "Date"],
                            data: data.failed,
                            // pagination: {
                            //     limit: 3,
                            //     summary: false
                            // }
                        }).render(document.getElementById("failed"));
                        first = false;
                    }else{
                        current.updateConfig({
                            data: data.working
                        }).forceRender();
                        
                        success.updateConfig({
                            data: data.success
                        }).forceRender();
                        
                        fail.updateConfig({
                            data: data.failed
                        }).forceRender();
                    }
                    // successList = data.success
                    // $('#successList').html('')
                    // if(Array.isArray(successList) && Object.keys(successList).length > 0)
                    // {
                    //     Object.keys(successList).forEach(function(key) {
                    //         $('#successList').append(`
                    //     <tr class="text-gray-700 dark:text-gray-400 locked_item" data-id="${key}">
                    //         <td class="px-4 py-3 text-sm">${successList[key]['request'] ?? "⁉"}</td>
                    //         <td class="px-4 py-3 text-sm">${successList[key]['service'] ?? "⁉"}</td>
                    //         <td class="px-4 py-3 text-sm">${successList[key]['date'] ?? "⁉"}</td>
                    //     </tr>
                    // `)
                    //     });
                    // }

                    // failedList = data.failed
                    // $('#failedList').html('')
                    // if(Array.isArray(failedList) && Object.keys(failedList).length > 0)
                    // {
                    //     Object.keys(failedList).forEach(function(key) {
                    //         $('#failedList').append(`
                    //     <tr class="text-gray-700 dark:text-gray-400 locked_item" data-id="${key}">
                    //         <td class="px-4 py-3 text-sm">${failedList[key]['request'] ?? "⁉"}</td>
                    //         <td class="px-4 py-3 text-sm">${failedList[key]['service'] ?? "⁉"}</td>
                    //         <td class="px-4 py-3 text-sm">${failedList[key]['reason'] ?? "⁉"}</td>
                    //         <td class="px-4 py-3 text-sm">${failedList[key]['date'] ?? "⁉"}</td>
                    //     </tr>
                    // `)
                    //     });
                    // }
                },
                error: function (e) {
                }
            });
        })
    </script>
@endsection