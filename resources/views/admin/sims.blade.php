@extends('layouts.app')

@section('title')
    Danh Sách Sim
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Quản Lý Sim </h2>
    <!-- Modal backdrop. This what you want to place close to the closing body tag -->
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
                class="w-full px-6 py-4 overflow-hidden bg-white rounded-t-lg dark:bg-gray-800 sm:rounded-lg sm:m-4 sm:max-w-xl"
                role="dialog"
                id="modal"
        >
            <!-- Modal body -->
            <div class="mt-4 mb-6">
                <!-- Modal title -->
                <p class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300" >
                    Đang chỉnh sửa sim: <span id="phone"></span> <small class="text-gray-400">(ID: <span id="simId"></span>)</small>
                </p>
                <!-- Modal description -->
                <form>

                    <label class="block mt-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">
                          Trạng Thái
                        </span>
                        <select id="status" class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                            <option value="0">Bảo Trì</option>
                            <option value="1">Trống</option>
                            <option value="2">Đang Xử Lí</option>
                        </select>
                    </label>

                    <label class="block mt-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">
                          Nhà Mạng
                        </span>
                        <select id="network" class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                            @foreach($data['networks'] as $network)
                                <option value="{{$network['uniqueId']}}">{{ $network['networkName'] }} @if($network['status'] != 1) (Bảo Trì) @endif</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block mt-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">
                          Dịch vụ đang khoá
                        </span>
                        <div class="w-full mb-8 overflow-hidden bg-white rounded-lg shadow-md">
                            <div class="w-full overflow-x-auto">
                                <table class="w-full whitespace-no-wrap">
                                    <thead>
                                    <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                                        <th class="px-4 py-3">Dịch vụ</th>
                                        <th class="px-4 py-3">Hết hạn</th>
                                        <th class="px-4 py-3">Hành động</th>
                                    </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800" id="lockedList">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </label>

                    <button id="simEdit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"> Lưu thông tin </button>
                </form>

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
    <!-- End of modal backdrop -->
    <div class="my-6 justify-center flex-1 lg:mr-32">
        <div class="relative w-full max-w-xl mr-6 focus-within:text-purple-500" >
            <button data-type="lock" class="action px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                Khoá tất cả sim đã chọn
            </button>

            <button data-type="delete" class="action px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                Xoá tất cả sim đã chọn
            </button>
        </div>
    </div>

    <div class="w-full mb-8 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <style>
                #sims th {
                    font-size: .8rem!important;
                    text-align: center ;
                    margin-bottom: auto;
                    margin-top: auto;
                }
            </style>
            <div id="sims"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        const dataList = [
                @foreach($data['sims'] as $task)
            [
                "{{ $task['uniqueId'] }}",
                "{{ $task['phone'] }}",
                "{{ $task['networkId'] }}",
                formatStatus({{ $task['status'] }}),
                "{{ $task['success'] }}",
                "{{ $task['failed'] }}",
                "{{ $task['created_at'] }}",
                "{{ $task['updated_at'] }}",
                gridjs.html(`
                    <div class="flex items-center space-x-4 text-sm">
                                <button @click="openModal" data-sim="{{ $task['uniqueId'] }}" class="editBtn flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Edit">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </button>
                                <button data-sim="{{ $task['uniqueId'] }}" class="delete flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Delete">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                    `),
            ],
            @endforeach
        ];

        $(".action").click(function (e) {
            e.preventDefault()
            const selected = $(".gridjs-tr-selected")
            var action = $(this).attr('data-type') // var cause we gonna need it for ajax func
            var tempObj = {};
            if (selected.length <= 0) return 0; // can change this to a notification if possible
            selected.each(function (e) {
                const userid = $(this).children('[data-column-id=id]').attr('data-user')
                tempObj[Object.keys(tempObj).length + 1] = userid
            })
            $.ajax({
                type: "POST",
                url: "{{ route('admin.bulkEdit') }}",
                cache: false,
                data: JSON.stringify({
                    type: action,
                    data: tempObj
                }),
                processData: false,
                contentType: "application/json; charset=UTF-8",
                success: function (data) {
                    if(data.status > 200)
                    {
                        return vt.error(data.message, {
                            title: "Lỗi",
                            position: "top-right",
                        })
                    }
                    vt.success("Đã lưu thông tin sim", {
                        title: "Thành công",
                        position: "top-right",
                    })
                    location.reload();
                },
                error: function (e) {
                    return vt.error(e, {
                        title: "Lỗi",
                        position: "top-right",
                    })
                }
            });
        })

        function fillToModal(data) {
            if(jQuery.isEmptyObject(data)) return alert('Không tìm thấy');
            $('#simId').text(data.uniqueId);
            $('#simEdit').attr('data-sim', data.uniqueId);
            $('#phone').text(data.phone);

            locked_list = JSON.parse(data.locked_services)
            $('#lockedList').html('')
            if(Array.isArray(locked_list) && Object.keys(locked_list).length > 0)
            {
                Object.keys(locked_list).forEach(function(key) {
                    $('#lockedList').append(`
                        <tr class="text-gray-700 dark:text-gray-400 locked_item" data-id="${key}">
                            <td class="px-4 py-3 text-sm">${locked_list[key]['name'] ?? "⁉"}</td>
                            <td class="px-4 py-3 text-sm">${moment().format('HH:mm:ss DD-MM-Y')}</td>
                            <td class="px-4 py-3 text-sm d-flex justify-center delete-locked-item" data-locked="${key}" data-sim="${data.uniqueId}">
                              <button class="delete flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    `)
                });
            }

            $('#simEdit').attr('data-sim', data.uniqueId);
            $(`#status option[value=${data.status}]`).prop('selected', 'selected').change();
            $(`#network option[value=${data.networkId}]`).prop('selected', 'selected').change();
        }

        function formatStatus(status) {
            value = ``;
            switch (status) {
                case 0:
                    value = `Offline`
                    break;
                case 1:
                    value = `Online`
                    break;
                case 2:
                    value = `Working`
                    break;
            }
            return value;
        }

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            const simId = $(this).attr('data-sim');
            var x = window.confirm("Bạn có chắc muốn xoá sim này?");
            if(!x) return false;
            $.ajax({
                type: "POST",
                url: `{{ route('admin.simEdit') }}?id=${simId}&delete=1`,
                cache: false,
                success: function (data) {
                    console.log(data)
                    if(data.status > 200)
                    {
                        return vt.error(data.message, {
                            title: "Lỗi",
                            position: "top-right",
                        })
                    }
                    vt.success("Đã xoá sim thành công", {
                        title: "Thành công",
                        position: "top-right",
                    })
                    location.reload();
                },
                error: function (e) {
                    return vt.error(e, {
                        title: "Lỗi",
                        position: "top-right",
                    })
                }
            });
        })

        $(document).on('click','#simEdit',function(e){
            e.preventDefault();
            const simId = $(this).attr('data-sim');
            const status = $('#status').val();
            const network = $('#network').val();

            $.ajax({
                type: "POST",
                url: `{{ route('admin.simEdit') }}?id=${simId}&status=${status}&network=${network}`,
                cache: false,
                success: function (data) {
                    if(data.status > 200)
                    {
                        return vt.error(data.message, {
                            title: "Lỗi",
                            position: "top-right",
                        })
                    }
                    vt.success("Đã lưu thông tin người dùng", {
                        title: "Thành công",
                        position: "top-right",
                    })
                    location.reload();
                },
                error: function (e) {
                    return vt.error(e, {
                        title: "Lỗi",
                        position: "top-right",
                    })
                }
            });
        })

        $(document).on('click', '.editBtn', function (e) {
            e.preventDefault()
            const simId = $(this).attr('data-sim');

            $.ajax({
                type: "POST",
                url: "{{ route('admin.sims') }}/"+simId,
                cache: false,
                success: function (data) {
                    fillToModal(data)
                },
                error: function (e) {
                }
            });
        })

        $(document).on('click','.delete-locked-item',function(e){
            e.preventDefault()
            serviceId = $(this).attr('data-locked')
            simId = $(this).attr('data-sim')

            $.ajax({
                type: "POST",
                url: "{{ route('admin.removeLockedService') }}",
                cache: false,
                data: JSON.stringify({
                    simId: simId,
                    serviceId: serviceId
                }),
                processData: false,
                contentType: "application/json; charset=UTF-8",
                success: function (data) {
                    if(data.status > 200)
                    {
                        return vt.error(data.message, {
                            title: "Lỗi",
                            position: "top-right",
                        })
                    }
                    vt.success("Đã lưu thông tin sim", {
                        title: "Thành công",
                        position: "top-right",
                    })
                    location.reload();
                },
                error: function (e) {
                    return vt.error(e, {
                        title: "Lỗi",
                        position: "top-right",
                    })
                }
            });
        });
        $( document ).ready(function() {
            const grid = new gridjs.Grid({
                columns: [
                    {
                        id: 'myCheckbox',
                        name: '#',
                        plugin: {
                            // install the RowSelection plugin
                            component: gridjs.plugins.selection.RowSelection,
                        }
                    },
                    { name: 'ID',
                        attributes: (cell) => {
                            if (cell) {
                                return {
                                    'data-user':cell
                                };
                            }
                        }},
                    "SDT",
                    "Nhà mạng",
                    "Trạng thái",
                    "Thành công",
                    "Thất bại",
                    "Tạo vào",
                    "Update lần cuối",
                    {
                        name: "Hành Động",
                        sort: false
                    }
                ],
                data: dataList,
                styles: {
                    th: {
                        'font-size': '8px !important'
                    }
                },
                search: true,
                sort: {
                    multiColumn: true
                },

                pagination: true
            }).render(document.getElementById("sims"));

            function validateEmail($email) {
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                return emailReg.test( $email );
            }

            $('#searchBtn').click(function (e) {
                e.preventDefault()
                const simId = $('#searchInput').val();

                $.ajax({
                    type: "POST",
                    url: "{{ url('/admin/phone/') }}/"+simId,
                    cache: false,
                    success: function (data) {
                        fillToModal(data)
                    },
                    error: function (e) {
                    }
                });
            })
        });
    </script>
@endsection