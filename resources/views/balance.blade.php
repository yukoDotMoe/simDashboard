@extends('layouts.app')

@section('title')
    Balance
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Balance <p class="text-xs text-gray-600 dark:text-gray-200">
            Store your payment information safely and easily access your funds.
        </p></h2>
    <div class="mb-4 relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400 shadow-md">
        <input name="daterange" class="block w-full pl-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Jane Doe">
        <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
            </svg>
        </div>
    </div>
    <!-- Cards -->
    <div class="grid gap-6 mb-8 md:grid-cols-3 xl:grid-cols-3">
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Current Available Balance </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ number_format(Auth::user()->balance, 0, '', ',') }}</p>
            </div>
        </div>

        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Total Spent </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalSpent"><i class="fa-solid fa-circle-notch fa-spin"></i></p>
            </div>
        </div>

        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-500 dark:bg-blue-100">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Total Top-up </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalTopup"><i class="fa-solid fa-circle-notch fa-spin"></i></p>
            </div>
        </div>
    </div>

    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Account's Balance Activities</h2>
    <div class="w-full mb-8 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <div id="payments"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $( document ).ready(function() {
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

            let transactionsTable;
            let firstTime = true;
            let totalSpent

            function updateTable(data) {
                var fixedArray = [];
                if(Array.isArray(data) && Object.keys(data).length > 0)
                {
                    Object.keys(data).forEach(function(key) {
                        const row = data[key]
                        fixedArray.push({
                            date: row.date,
                            amount: row.type + (new Intl.NumberFormat('vi-VN').format(parseInt(row.amount)).replaceAll(".", ",")),
                            category: gridjs.html(formatStatus(row.status)),
                            description: row.reason
                        })
                    });
                }
                if(firstTime)
                {
                    transactionsTable = new gridjs.Grid({
                        columns: ["Date", "Amount", "Category", "Description"],
                        data: fixedArray,
                        search: true,
                        sort: {
                            multiColumn: false
                        },
                        pagination: true
                    }).render(document.getElementById("payments"));
                    firstTime = false;
                }else{
                    transactionsTable.updateConfig({
                        data: fixedArray
                    }).forceRender();
                }
            }

            function fillContent(start, end)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ route('balance.filter') }}",
                    data: { startDate: start, endDate: end},
                    cache: false,
                    dataType: "json",
                    encode: true,
                    success: function (data) {
                        updateTable(data.data.transactions)
                        $('#totalSpent').html((new Intl.NumberFormat('vi-VN').format(parseInt(data.data.spent)).replaceAll(".", ",")))
                        $('#totalTopup').html((new Intl.NumberFormat('vi-VN').format(parseInt(data.data.topup)).replaceAll(".", ",")))
                    },
                    error: function (e) {
                    }
                });
            }
        })

        function formatStatus(status)
        {
            var tobereturn = ``
            switch (parseInt(status)) {
                case 0:
                    tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                          Refunded
                        </span>
                        `
                    break;
                case 1:
                    tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-700">
                                            Charged
                                        </span>
                        `
                    break;
                case 2:
                    tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                                          On-hold
                                        </span>
                        `
                    break;
                case 3:
                    tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                          Top-up
                                        </span>
                        `
                    break;
                case 4:
                    tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                                          Adjusted
                                        </span>
                        `
                    break;
                case 5:
                    tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-700">
                                            Charged
                                        </span>
                        `
                    break;
            }

            return tobereturn
        }
    </script>
@endsection