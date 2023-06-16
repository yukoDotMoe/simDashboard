@extends('layouts.app')

@section('title')
    Rent History
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Rent History <p class="text-xs text-gray-600 dark:text-gray-200">
            Access your previous rentals and keep track of your sim usage.
        </p></h2>

    <div class="w-full mb-8 overflow-hidden">
        <div class="mb-4 relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400 shadow-md">
            <input name="daterange" class="block w-full pl-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Jane Doe">
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
                </svg>
            </div>
        </div>
        <div class="w-full overflow-x-auto">
            <div id="requests"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $( document ).ready(function() {
            function copyToClipboard(text) {
                var sampleTextarea = document.createElement("textarea");
                document.body.appendChild(sampleTextarea);
                sampleTextarea.value = text;
                sampleTextarea.select();
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

            function formatStatus(status)
            {
                var tobereturn = ``
                switch (parseInt(status)) {
                    case 0:
                        tobereturn = `Timeout`
                        break;
                    case 1:
                        tobereturn = `Completed`
                        break;
                    case 2:
                        tobereturn = `Waiting`
                        break;
                }
                return tobereturn;
            }

            let requestsTable;
            let firstTime = true;

            function updateTable(data) {
                var fixedArray = [];
                if(Array.isArray(data) && Object.keys(data).length > 0)
                {
                    Object.keys(data).forEach(function(key) {
                        const row = data[key]
                        fixedArray.push({
                            id: row.id,
                            service: row.service,
                            price: (new Intl.NumberFormat('vi-VN').format(parseInt(row.price)).replaceAll(".", ",")),
                            number: row.phone,
                            status: gridjs.html(formatStatus(row.status)),
                            code: row.code,
                            date: row.date
                        })
                    });
                }
                if(firstTime)
                {
                    requestsTable = new gridjs.Grid({
                        columns: ["ID", "Service", "Price", "Number","Status", "Code", "Date"],
                        data: fixedArray,
                        search: true,
                        sort: {
                            multiColumn: false
                        },
                        pagination: true
                    }).render(document.getElementById("requests"));
                    firstTime = false;
                }else{
                    requestsTable.updateConfig({
                        data: fixedArray
                    }).forceRender();
                }
            }


            function fillContent(start, end)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ route('rentHistory.filter') }}",
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
        });
    </script>
@endsection