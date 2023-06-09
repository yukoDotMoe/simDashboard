@extends('layouts.app')

@section('title')
    Vendor's Dashboard
@endsection

@section('content')
    <div class="mb-4 relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400 shadow-md">
        <input name="daterange" class="block w-full pl-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Jane Doe">
        <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
            </svg>
        </div>
    </div>

    <div class="grid gap-6 mb-4 md:grid-cols-2 xl:grid-cols-2">
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Total Profit</p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalProfit"><i class="fa-solid fa-circle-notch fa-spin"></i></p>
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
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Total Request Served</p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalRequest"><i class="fa-solid fa-circle-notch fa-spin"></i></p>
            </div>
        </div>

    </div>

    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Transactions </h2>
    <div class="w-full mb-8 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <div id="payments" class="text-center"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
        </div>
    </div>

    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Requests </h2>
    <div class="w-full mb-8 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <div id="request" class="text-center"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            console.log( "\u001b[1;31m You shouldn't open this panel you know. But welcome, {{ Auth::user()->email }}" );

            $('input[name="daterange"]').daterangepicker({
                "autoApply": true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                "alwaysShowCalendars": true,
                "startDate": moment().subtract(7, 'days'),
                "endDate": moment()
            }, function(start, end, label) {
                fillContent(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))
            });

            fillContent(
                moment().subtract(7, 'days').format('YYYY-MM-DD'),
                moment().format('YYYY-MM-DD'),
            )

            let paymentsTable;
            let requestTable;
            let firstTime = true;
            let totalProfit = 0;
            let totalRequest = 0;

            function updateTable(data)
            {
                if(firstTime)
                {
                    $('#payments').html(``)
                    $('#request').html(``)
                    firstTime = false;
                    paymentsTable = new gridjs.Grid({
                        columns: ["ID", "Amount", "Request", "Date"],
                        data: data.transactions,
                        // search: true,
                        sort: {
                            multiColumn: false
                        },
                        // pagination: true
                    }).render(document.getElementById("payments"));

                    data.transactions.forEach(function (e) {
                        totalProfit += e.amount;
                    })

                    $('#totalProfit').html(totalProfit)
                    $('#totalRequest').html(Object.keys(data.requests).length)

                    requestTable = new gridjs.Grid({
                        columns: ["ID", "Phone", "Service", "Status", "Date"],
                        data: data.requests,
                        // search: true,
                        sort: {
                            multiColumn: false
                        },
                        // pagination: true
                    }).render(document.getElementById("request"));
                }else{
                    paymentsTable.updateConfig({
                        data: data.transactions
                    }).forceRender();

                    requestTable.updateConfig({
                        data: data.requests
                    }).forceRender();
                }
            }

            function fillContent(start, end)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ route('vendor.dashboard.filter') }}",
                    data: { startDate: start, endDate: end},
                    cache: false,
                    dataType: "json",
                    encode: true,
                    success: function (data) {
                        updateTable(data.data)
                    },
                    error: function (e) {
                    }
                });
            }
        })
    </script>
@endsection