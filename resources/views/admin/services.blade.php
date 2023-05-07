@extends('layouts.app')

@section('title')
    Danh Sách Dịch Vụ
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Quản Lý Dịch Vụ </h2>
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
                    Đang chỉnh sửa dịch vụ: <span id="serviceName"></span> <small class="text-gray-400">(ID: <span id="serviceId"></span>)</small>
                </p>
                <!-- Modal description -->
                <form>

                    <label class="block mt-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">
                          Trạng Thái
                        </span>
                        <select id="status" class="block w-full mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-select focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray">
                            <option value="0">Bảo Trì</option>
                            <option value="1">Hoạt Động</option>
                        </select>
                    </label>

                    <label class="mb-4 block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Giá</span>
                        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                               id="price" type="number" required />
                    </label>

                    <button id="serviceBtn" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"> Lưu thông tin </button>
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
            <div class="absolute inset-y-0 flex items-center pl-2">
                <svg
                        class="w-4 h-4"
                        aria-hidden="true"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                >
                    <path
                            fill-rule="evenodd"
                            d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                            clip-rule="evenodd"
                    ></path>
                </svg>
            </div>
            <div class="relative text-gray-500 focus-within:text-purple-600" >
                <input class="block w-full pr-20 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Tìm theo tên dịch vụ" id="searchInput" />
                <button id="searchBtn" @click="openModal" class="absolute inset-y-0 right-0 px-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-r-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Tìm kiếm
                </button>
            </div>

        </div>
        <div class="mt-4">
            <button onclick="location.href='{{ route('admin.createService') }}';"
                    class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
            >
                <span>Tạo Dịch Vụ</span>

                <svg class="w-4 h-4 ml-2 -mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 23 23" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </button>
        </div>
    </div>

    <div class="w-full mb-8 overflow-hidden bg-white rounded-lg shadow-md">
        <div class="w-full overflow-x-auto">
            <table class="w-full whitespace-no-wrap">
                <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Tên Dịch Vụ</th>
                    <th class="px-4 py-3">Giá</th>
                    <th class="px-4 py-3">Trạng thái</th>
                    <th class="px-4 py-3">Tạo Vào</th>
                    <th class="px-4 py-3">Hành Động</th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                @foreach($data['services'] as $task)
                    <tr class="text-gray-700 dark:text-gray-400">
                        <td class="px-4 py-3 text-sm">{{ $task['uniqueId'] }}</td>
                        <td class="px-4 py-3 text-sm">{{ $task['serviceName'] }}</td>
                        <td class="px-4 py-3 text-sm">{{ $task['price'] }}</td>
                        <td class="px-4 py-3 text-sm">
                            @switch($task['status'])
                                @case(0)
                                    <span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full dark:text-red-100 dark:bg-red-700">
                                          Bảo trì
                                    </span>
                                    @break
                                @case(1)
                                    <span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100">
                                            Hoạt Động
                                        </span>
                                    @break
                            @endswitch </td>
                        <td class="px-4 py-3 text-sm">{{ $task['created_at'] }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-4 text-sm">
                                <button @click="openModal" data-sim="{{ $task['id'] }}" class="editBtn flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Edit">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </button>
                                <button data-sim="{{ $task['id'] }}" class="delete flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Delete">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {!! $data['services']->links() !!}

        </div>
    </div>
@endsection

@section('js')
    <script>
        $( document ).ready(function() {
            function fillToModal(data) {
                if(jQuery.isEmptyObject(data)) return alert('Không tìm thấy');
                $('#serviceName').text(data.serviceName);
                $('#serviceId').text(data.uniqueId);
                $('#price').val(data.price);
                $('#serviceBtn').attr('data-sim',data.id);
                $(`#status option[value=${data.status}]`).prop('selected', 'selected').change();

            }

            $('#serviceBtn').click(function (e) {
                e.preventDefault();
                const simId = $(this).attr('data-sim');
                const status = $('#status').val();
                const price = $('#price').val();

                $.ajax({
                    type: "POST",
                    url: `{{ route('admin.serviceEdit') }}?id=${simId}&status=${status}&price=${price}`,
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

            $('.delete').click(function (e) {
                e.preventDefault();
                const simId = $(this).attr('data-sim');
                var x = window.confirm("Bạn có chắc muốn xoá sim này?");
                if(!x) return false;
                $.ajax({
                    type: "POST",
                    url: `{{ route('admin.serviceEdit') }}?id=${simId}&delete=1`,
                    cache: false,
                    success: function (data) {
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

            $('.editBtn').click(function (e) {
                e.preventDefault()
                const simId = $(this).attr('data-sim');

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.services') }}/"+simId,
                    cache: false,
                    success: function (data) {
                        fillToModal(data)
                    },
                    error: function (e) {
                    }
                });
            })

            $('#searchBtn').click(function (e) {
                e.preventDefault()
                const simId = $('#searchInput').val();

                $.ajax({
                    type: "POST",
                    url: "{{ url('/admin/services/') }}/"+simId,
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