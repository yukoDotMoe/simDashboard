@extends('layouts.app')

@section('title')
    Tổng Quan Admin
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Tổng Quan Admin </h2>
    <div class="grid gap-6 mb-4 md:grid-cols-2 xl:grid-cols-4">
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Tổng số lượng sim</p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"> {{ number_format($data['count']['sims']['alive']) }} đang chạy ({{ number_format($data['count']['sims']['died']) }} đã chết) </p>
            </div>
        </div>
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Tổng số lần các dịch vụ được dùng </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"> {{ number_format($data['count']['requests']['normal']) }} ({{ number_format($data['count']['requests']['failed']) }} thất bại)</p>
            </div>
        </div>
        <!-- Card -->
        <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="p-3 mr-4 text-teal-500 bg-teal-100 rounded-full dark:text-teal-100 dark:bg-teal-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Tổng số lượng người dùng </p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"> {{ number_format($data['count']['users']['normal']) }}</p>
            </div>
        </div>
        <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
            <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Số dư của tổng người dùng</p>
                <p class="text-lg font-semibold text-gray-700 dark:text-gray-200"> {{ number_format($data['count']['users']['balances']) }} ₫</p>
            </div>
        </div>
    </div>

{{--    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">--}}
{{--        <p class="text-sm text-gray-600 dark:text-gray-400">--}}
            <div class="mb-4 relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400 shadow-md">
                <input name="daterange" class="block w-full pl-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Jane Doe">
                <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5m-9-6h.008v.008H12v-.008zM12 15h.008v.008H12V15zm0 2.25h.008v.008H12v-.008zM9.75 15h.008v.008H9.75V15zm0 2.25h.008v.008H9.75v-.008zM7.5 15h.008v.008H7.5V15zm0 2.25h.008v.008H7.5v-.008zm6.75-4.5h.008v.008h-.008v-.008zm0 2.25h.008v.008h-.008V15zm0 2.25h.008v.008h-.008v-.008zm2.25-4.5h.008v.008H16.5v-.008zm0 2.25h.008v.008H16.5V15z"></path>
                    </svg>
                </div>
            </div>

            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-2">
                <!-- Card -->
                <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                    <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Tổng số tiền người dùng đã nạp </p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalTopup"><i class="fa-solid fa-circle-notch fa-spin"></i></p>
                    </div>
                </div>
                <!-- Card -->
                <div class="flex items-center p-4 bg-white rounded-lg shadow-md dark:bg-gray-800">
                    <div class="p-3 mr-4 text-red-600 bg-red-100 rounded-full dark:text-red-600 dark:bg-red-500">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400"> Số tiền người dùng đã tiêu </p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-200" id="totalCharged"><i class="fa-solid fa-circle-notch fa-spin"></i></p>
                    </div>
                </div>
            </div>
            <div class="w-full mb-8 overflow-hidden bg-white rounded-lg shadow-md">
                <div class="w-full overflow-x-auto">
                    <table class="w-full whitespace-no-wrap">
                        <thead>
                        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Người dùng</th>
                            <th class="px-4 py-3">Dịch vụ</th>
                            <th class="px-4 py-3">Trạng thái</th>
                            <th class="px-4 py-3">Số tiền</th>
                            <th class="px-4 py-3">Số dư cũ</th>
                            <th class="px-4 py-3">Số dư mới</th>
                            <th class="px-4 py-3">Sử dụng vào</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="activitiesTable">
                        </tbody>
                    </table>
                </div>
            </div>
{{--        </p>--}}
{{--    </div>--}}

    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Chỉnh sửa tài liệu API </h2>
    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <textarea id="apiContent">{{ $data['apiDoc']['apiDocs']['value'] }}</textarea>
            <button id="saveApi" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple" data-user="1"> Lưu thông tin </button>
        </p>
    </div>
@endsection

@section('js')
    <script>
        tinymce.init({
            selector: 'textarea',
            plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
            toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
        });

        $( document ).ready(function() {
            function calcTopup(data) {
                totalTopup = [];
                data.transactions.forEach(function (item) {
                    if(item.type == 'topup') totalTopup.push(parseInt(item.amount));
                })
                $('#totalTopup').text(totalTopup.reduce((partialSum, a) => partialSum + a, 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }))
            }

            function calcCharged(data) {
                totalCharged = [];
                data.transactions.forEach(function (item) {
                    if(item.type === 'minus' && item.status === 1) totalCharged.push(parseInt(item.amount));
                })
                $('#totalCharged').text(totalCharged.reduce((partialSum, a) => partialSum + a, 0).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }))
            }


            function statusBadge(status) {
                value = ``;
                switch (status) {
                    case 0:
                        value = `<span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                          Hoàn tiền
                        </span>`
                        break;
                    case 1:
                        value = `<span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-700">
                          Mua dịch vụ
                        </span>`
                        break;
                    case 2:
                        value = `<span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                          Giữ tiền
                        </span>`
                        break;
                    case 3:
                        value = `<span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                          Nạp tiền
                        </span>`
                        break;
                    case 4:
                        value = `<span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                          Điều chỉnh
                        </span>`
                        break;
                }
                return value;
            }

            function fillInTable(data) {
                tableDiv = $('#activitiesTable')
                tableDiv.html(``)
                data.transactions.forEach(function (item) {
                    if(item.status == 0 || item.status == 5) return;
                    tableDiv.append(`
                    <tr class="text-gray-700 dark:text-gray-400">
                        <td class="px-4 py-3 text-sm">${item.id}</td>
                        <td class="px-4 py-3 text-sm">${item.userid}</td>
                        <td class="px-4 py-3 text-sm">${(item.serviceName == null) ? 'Điều chỉnh' : item.serviceName}</td>
                        <td class="px-4 py-3 text-sm">${statusBadge(item.status)}</td>
                        <td class="px-4 py-3 text-sm">${item.typeText} ${parseInt(item.amount).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</td>
                        <td class="px-4 py-3 text-sm">${parseInt(item.old).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</td>
                        <td class="px-4 py-3 text-sm">${parseInt(item.new).toLocaleString('vi-VN', { style: 'currency', currency: 'VND' })}</td>
                        <td class="px-4 py-3 text-sm">${moment(item.date).lang("vi").format('llll')}</td>
                    </tr>
                    `)
                })
            }

            function fillContent(start, end, page = 1)
            {
                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.filter') }}",
                    data: { startDate: start, endDate: end, page },
                    cache: false,
                    dataType: "json",
                    encode: true,
                    success: function (data) {
                        data = data.data
                        console.log(data)
                        calcTopup(data)
                        calcCharged(data)
                        fillInTable(data)
                    },
                    error: function (e) {
                    }
                });
            }

            $(document).on('click', "#paginator a", function(e) {
                e.preventDefault();

                $('a').removeClass('active');
                $(this).parent('a').addClass('active');

                var page = $(this).attr('data-page');

                fillContent(page);
            });

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


            $('#saveApi').click(function (e) {
                e.preventDefault();
                var formData = {
                    apiContent: tinymce.activeEditor.getContent(),
                };
                $.ajax({
                    type: "POST",
                    url: `{{ route('admin.apiUpdate') }}`,
                    data: formData,
                    cache: false,
                    dataType: "json",
                    encode: true,
                    success: function (data) {
                        console.log(data)
                        if(data.status > 200)
                        {
                            return vt.error(data.message, {
                                title: "Lỗi",
                                position: "top-right",
                            })
                        }
                        vt.success("Đã tạo dịch vụ thành công", {
                            title: "Thành công",
                            position: "top-right",
                        })
                        window.location.href = "{{ route('admin.dashboard') }}";
                    },
                    error: function (e) {
                        return vt.error(e, {
                            title: "Lỗi",
                            position: "top-right",
                        })
                    }
                });
            })
        });
    </script>
@endsection