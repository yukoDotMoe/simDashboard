@extends('layouts.app')

@section('title')
    Renting Service
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Dịch vụ cho thuê
        <p class="text-xs text-gray-600 dark:text-gray-200">
            Chúng tôi có sẵn bộ lọc sim, bạn có thể thử mà không mất phí!
        </p></h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <label class="block text-sm">
                <span class="text-gray-700 dark:text-gray-400"> Chọn dịch vụ </span>
                <select id="serviceSelect" class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                @if(count($data['services']) == 0)
                    <option selected disabled>Không có dịch vụ có sẵn.</option>
                @else
                    @foreach($data['services'] as $service)
                        <option value="{{ $service['uniqueId'] }}" data-name="{{ $service['serviceName'] }}" data-price="{{ number_format($service['price'],0,'',',') }}">{{ $service['serviceName'] }} ({{ number_format($service['price'],0,'',',') }} VND)</option>
                    @endforeach
                @endif
                </select>
            </label>
            <div class="flex items-center mt-4 mb-4">
                <input id="checkbox-2" type="checkbox" value="" class="border-2 p-3 rounded-lg w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                <label for="checkbox-2" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300 ">Sử dụng bộ lọc
                </label>
            </div>
            <div class="grid md:grid-cols-2 gap-6 hidden" id="valueDiv">
                <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-400"> Chọn nhà mạng </span>
                    <select id="networkSelect" class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                        @if(count($data['networks']) == 0)
                            <option selected disabled>Không có nhà mạng có sẵn.</option>
                        @else
                            <option value="all" data-name="all">Nhà mạng bất kì</option>
                            @foreach($data['networks'] as $service)
                                <option value="{{ $service['uniqueId'] }}" data-name="{{ $service['networkName'] }}">{{ $service['networkName'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </label>

                <label class="mb-4 block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Số nhất định</span>
                    <input placeholder="Thuê được nếu số này đang trống"
                           id="phoneNumber" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input">
                </label>
            </div>
            <button id="rentSubmit" type="submit" class="block w-full @if(count($data['services']) == 0)
            px-4 py-2 mt-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg opacity-50 cursor-not-allowed focus:outline-none
@else
px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple
@endif"> Thuê sim </button>
        </p>
    </div>

    <div class="w-full mb-8 overflow-hidden bg-white rounded-lg shadow-md">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Dịch vụ</th>
                    <th class="px-4 py-3">Giá</th>
                    <th class="px-4 py-3">Số</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Code</th>
                    <th class="px-4 py-3">Thời gian</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="rentTable">
                    @foreach($data['workingTask'] as $task)
                        <tr class="text-gray-700 dark:text-gray-400" id="{{ $task['uniqueId'] }}">
                            <td class="px-4 py-3 text-sm" id="serviceName"> {{ $task['uniqueId'] }} </td>
                            <td class="px-4 py-3 text-sm" id="serviceName"> {{ $task['serviceName'] }} </td>
                            <td class="px-4 py-3 text-sm" id="servicePrice"> {{ number_format($task['servicePrice'], 0, '', ',') }} </td>
                            <td class="px-4 py-3 text-sm phoneNumber" id="phoneNumber"> {{  $task['phone'] }} </td>
                            <td class="px-4 py-3 text-sm" id="status">
                                <span class="px-2 py-1 font-semibold leading-tight text-orange-700 bg-orange-100 rounded-full dark:bg-orange-700 dark:text-orange-100">
                                  Đang chờ
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm code" id="code"></td>
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
        function copyToClipboard(text) {
            var sampleTextarea = document.createElement("textarea");
            document.body.appendChild(sampleTextarea);
            sampleTextarea.value = text; //save main text in it
            sampleTextarea.select(); //select textarea contenrs
            document.execCommand("copy");
            document.body.removeChild(sampleTextarea);
            vt.success(`Đã sao chép "${text}".`, {
                title: "Thành công",
                position: "top-right",
            })
        }

        $(document).on('click', '.code, .phoneNumber', function () {
            const text = $(this).text();
            if(!text) return false
            copyToClipboard(text)
        })

        $( document ).ready(function() {
            $('#checkbox-2').change(function (e) {
                valueDiv = $('#valueDiv')
                if($(this).prop('checked'))
                {
                    if(valueDiv.hasClass('hidden')) valueDiv.removeClass('hidden')
                }else{
                    valueDiv.addClass('hidden')
                }
            })

            function addToTable(id, name, price, phone, time)
            {
                const table = $('#rentTable');
                const template = `
                <tr class="text-gray-700 dark:text-gray-400" id="${id}">
                        <td class="px-4 py-3 text-sm" id="uniqueId"> ${id} </td>
                        <td class="px-4 py-3 text-sm" id="serviceName"> ${name} </td>
                        <td class="px-4 py-3 text-sm" id="servicePrice"> ${(new Intl.NumberFormat('vi-VN').format(parseInt(price)).replaceAll(".", ","))} </td>
                        <td class="px-4 py-3 text-sm phoneNumber" id="phoneNumber"> ${phone} </td>
                        <td class="px-4 py-3 text-sm" id="status">${waitingBadge}</td>
                        <td class="px-4 py-3 text-sm code" id="code"> </td>
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
            });

            channel.bind('simFailed', function(data) {
                const tableRow = $(`tr[id="${data.uniqueId}"]`);
                tableRow.children('#status').html(failedBadge);
                return vt.error("Do hết thời gian, yêu cầu thuê của bạn đã bị huỷ và được hoàn tiền.", {
                    title: "Thuê thất bại",
                    position: "top-right",
                })
            });

            $('#rentSubmit').click(function () {
                const selection = $('#serviceSelect').val();
                const network = $('#networkSelect').val();
                const phone = $('#phoneNumber').val();
                el = $(this)
                
                el.html(`<i class="fa-solid fa-circle-notch fa-spin"></i>`)
                el.prop( "disabled", true )

                if(selection == null)
                {
                    return vt.error("Không lựa chọn nào có sẵn", {
                        title: "Error",
                        position: "top-right",
                    })
                }

                $.ajax({
                    url: `{{ route('rentFunc') }}?service=${selection}&network=${network}&number=${phone}`,
                    type: "POST",
                    cache: false,
                    success: function (data) {
                        el.html('Rent')
                        el.prop( "disabled", false )
                        if (data.status != 200)
                        {
                            var msgErr = ''
                            switch (data.message) {
                                case 'No phone number available':
                                    msgErr = 'Không sim nào có sẵn'
                                    break;
                                case 'Phone number not available':
                                    msgErr = 'Sim không khả dụng'
                                    break;
                                case 'Phone not found':
                                    msgErr = 'Sim không tồn tại'
                                    break;
                                case 'Your balance is not satisfied the transaction, please top-up more':
                                    msgErr = 'Số dư không đủ'
                                    break;
                                default:
                                    break;
                            }
                            return vt.error(msgErr, {
                                title: "Không thành công",
                                position: "top-right",
                            })
                        }
                        data = data.data
                        vt.success(`Yêu cầu đã được tạo và chờ xử lí.`, {
                            title: "Thuê thành công",
                            position: "top-right",
                        })
                        addToTable(data.requestId, data.name, data.price, data.phone, data.createdTime)
                    },
                    error: function (e) {
                    }
                });

            })
        });
    </script>
@endsection