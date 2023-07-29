@extends('layouts.app')

@section('title')
    Danh Sách Người Dùng
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Đang xem lịch sử của người dùng: <strong id="user">{{ $user->username }}</strong></h2>
    <div class="mb-4 relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400 shadow-md">
        <input name="daterange" class="block w-full pl-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Jane Doe">
        <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
            </svg>
        </div>
    </div>
    <div class="w-full mb-8 overflow-hidden rounded-lg">
        <div class="w-full overflow-x-auto">
            <div id="payments"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        let firstTime = true;
        let payments;
        $(document).ready(function () {
            function formatStatus(status) {
                value = ``;
                switch (status) {
                    case 0:
                        value = `Hoàn tiền`
                        break;
                    case 1:
                        value = `Mua`
                        break;
                    case 2:
                        value = `Giữ tiền`
                        break;
                    case 3:
                        value = `Nạp tiền`
                        break;
                    case 3:
                        value = `Trừ tiền`
                        break;
                }
                return value;
            }


            function fillInTable(data) {
                transactionsPayment = [];

                data.transactions.forEach(function (item) {
                    transactionsPayment.push({
                        id: item.id,
                        request: item.request,
                        service: (item.serviceName == null) ? 'Admin' : item.serviceName,
                        status: gridjs.html(formatStatus(item.status)),
                        change: `${parseInt(item.amount).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}`,
                        oldBal: parseInt(item.old).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }),
                        newBal: parseInt(item.new).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }),
                        date: moment(item.date).lang("vi").format('llll')
                    })
                })

                if(firstTime)
                {
                    $('#payments').html(``)
                    firstTime = false;
                    payments = new gridjs.Grid({
                        columns: ["ID",{
                            name: "Service",
                            id: "service"
                        },{
                            name: "Request",
                            id: "request"
                        },{
                            name: "Type",
                            id: "status"
                        },{
                            name: "Amount",
                            id: "change"
                        },{
                            name: "Old",
                            id: "oldBal"
                        },{
                            name: "New",
                            id: "newBal"
                        },{
                            name: "Time",
                            id: "date"
                        }],
                        data: transactionsPayment,
                        search: true,
                        sort: {
                            multiColumn: true
                        },
                        pagination: {
                            limit: 30,
                            summary: false
                        }
                    }).render(document.getElementById("payments"));
                }else{
                    payments.config.plugin.remove("pagination")
                    payments.config.plugin.remove("search")
                    payments.updateConfig({
                        data: transactionsPayment,
                        pagination: {
                            limit: 30,
                            summary: false
                        },
                        search: true,
                    }).forceRender();
                }
            }


            $(document).on('click', "#paginator a", function(e) {
                e.preventDefault();

                $('a').removeClass('active');
                $(this).parent('a').addClass('active');

                var page = $(this).attr('data-page');

                fillContent(page);
            });

            function fillContent(start, end, page = 1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.user.historyPost') }}",
                    data: { id: '{{ $user->id }}', startDate: start, endDate: end, page },
                    cache: false,
                    dataType: "json",
                    encode: true,
                    success: function (data) {
                        data = data.data
                        fillInTable(data)
                    },
                    error: function (e) {
                    }
                });
            }

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
                "startDate": moment(),
                "endDate": moment()
            }, function(start, end, label) {
                fillContent(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'))
            });

            fillContent(
                moment().format('YYYY-MM-DD'),
                moment().format('YYYY-MM-DD'),
            )
        })
    </script>
@endsection