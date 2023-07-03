@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Trang chủ <p class="text-xs text-gray-600 dark:text-gray-200">
            Quản lí thông tin giao dịch của bạn
        </p></h2>

    <!-- Cards -->
    <div class="grid gap-6 mb-8 md:grid-cols-3 xl:grid-cols-3">
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Trạng thái </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"> Hoạt động </p>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Số dư </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"> {{ number_format(Auth::user()->balance, 0, '', ',') }} </p>
            </div>
        </div>
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Số sim đã thuê </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"> {{ Auth::user()->totalRent }} </p>
            </div>
        </div>

    </div>

    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Lịch sử giao dịch    </h2>
    <div class="mb-4 relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400 shadow-md">
        <input name="daterange" class="block w-full pl-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Jane Doe">
        <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
            </svg>
        </div>
    </div>
    <div class="w-full mb-8 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <div id="payments"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $( document ).ready(function() {
            // Swal.fire({
            //     title: 'Announcements',
            //     text: 'Do you want to continue',
            // })
            $('input[name="daterange"]').daterangepicker({
                "autoApply": true,
                ranges: {
                    'Hôm nay': [moment(), moment()],
                    'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    '7 ngày qua': [moment().subtract(6, 'days'), moment()],
                    '30 ngày qua': [moment().subtract(29, 'days'), moment()],
                    'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                    'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
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
            let firstTime = true;

            function updateTable(data) {
                var fixedArray = [];
                if(Array.isArray(data) && Object.keys(data).length > 0)
                {
                    Object.keys(data).forEach(function(key) {
                        const row = data[key]
                        fixedArray.push({
                            date: row.date,
                            amount: row.type + (new Intl.NumberFormat('vi-VN').format(parseInt(row.amount)).replaceAll(".", ",")),
                            status: gridjs.html(formatStatus(row.status)),
                            request: row.request,
                            description: row.reason
                        })
                    });
                }
                if(firstTime)
                {
                    paymentsTable = new gridjs.Grid({
                        columns: [{
                            id: "date",
                            name: "Thời gian"
                        },{
                            id: "amount",
                            name: "Số tiền"
                        },{
                            id: "status",
                            name: "Trạng thái"
                        },{
                            name:  "ID",
                            id: "request"
                        }],
                        data: fixedArray,
                        search: true,
                        sort: {
                            multiColumn: false
                        },
                        pagination: true
                    }).render(document.getElementById("payments"));
                    firstTime = false;
                }else{
                    paymentsTable.updateConfig({
                        data: fixedArray
                    }).forceRender();
                }
            }


            function fillContent(start, end)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ route('dashboard.filter') }}",
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

            function formatStatus(status)
            {
                var tobereturn = ``
                switch (status) {
                    case 0:
                        tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                          Hoàn tiền
                        </span>
                        `
                        break;
                    case 1:
                        tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-700">
                                            Đã mua
                                        </span>
                        `
                        break;
                    case 2:
                        tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                                          Đang giữ
                                        </span>
                        `
                        break;
                    case 3:
                        tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                          Nạp tiền
                                        </span>
                        `
                        break;
                    case 4:
                        tobereturn = `
                        <span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                                          Trừ tiền
                                        </span>
                        `
                        break;
                }

                return tobereturn
            }
        })
    </script>
@endsection