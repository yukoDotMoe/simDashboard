@extends('layouts.app')

@section('title')
    Danh Sách Đại Lí
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Quản Lý Đại Lí </h2>
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
                    Đang chỉnh sửa người dùng: <span id="userId"></span>
                </p>
                <!-- Modal description -->
                <form>
                    <label class="mb-4 block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Tên</span>
                        <input id="username" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"/>
                    </label>

                    <label class="mb-4 block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Email</span>
                        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                               id="email" type="email" />
                    </label>

                    <label class="mb-4 block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Mật khẩu</span>
                        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                               id="password" type="text" />
                    </label>

                    <label class="mb-4 block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Phần trăm trên giao dịch</span>
                        <input id="profitRate" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"/>
                    </label>

                    <button id="accountEdit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"> Lưu thông tin </button>
                </form>
                <hr class="my-6">
                <form id="balanceForm">
                    <p class="mt-2 text-lg font-semibold text-gray-700 dark:text-gray-300" >
                        Điều chỉnh số dư
                    </p>
                    <div class="mb-4 mt-4 text-sm">
                        <span class="text-gray-700 dark:text-gray-400">
                          Loại chỉnh sửa
                        </span>
                        <div class="mt-2">
                            <label
                                    class="inline-flex items-center text-gray-600 dark:text-gray-400"
                            >
                                <input
                                        type="radio"
                                        class="text-purple-600 form-radio focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                                        name="editType"
                                        value="plus"
                                />
                                <span class="ml-2">Cộng</span>
                            </label>
                            <label
                                    class="inline-flex items-center ml-6 text-gray-600 dark:text-gray-400"
                            >
                                <input
                                        type="radio"
                                        class="text-purple-600 form-radio focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                                        name="editType"
                                        value="minus"
                                />
                                <span class="ml-2">Trừ</span>
                            </label>
                        </div>
                    </div>

                    <label class="mb-4 block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Số lượng</span>
                        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                               type="number" id="balanceAmount" min="1000" step="1000" required/>
                    </label>

                    <label class="mb-4 block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Lí do</span>
                        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                               id="balanceReason" required />
                    </label>

                    <button id="balanceEdit" type="submit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"> Điều chỉnh </button>
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
            <a href="{{ route('admin.vendors.create') }}" class="px-3 py-1 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                Tạo đại lí
            </a>
        </div>
    </div>

    <div class="w-full mb-8 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <style>
                #vendors th {
                    font-size: .8rem!important;
                    text-align: center ;
                    margin-bottom: auto;
                    margin-top: auto;
                }
            </style>
            <div id="vendors"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $( document ).ready(function() {
            $('#balanceEdit').click(function (e) {
                e.preventDefault();
                $('.error').each(function () {
                    $(this).remove()
                })
                const userid = $(this).attr('data-user');
                const amount = $('#balanceAmount');
                const reason = $('#balanceReason');
                const type = $('input[name="editType"]:checked');

                if(!type.val() || type.val() == '')
                {
                    return reason.parent().append(`
                    <span class="text-xs text-red-600 dark:text-red-400 error">
                      Vui lòng chọn loại
                    </span>`)
                }

                if(amount.val() < 0 || isNaN(amount.val()) )
                {
                    return amount.parent().append(`
                    <span class="text-xs text-red-600 dark:text-red-400 error">
                      Số lượng không hợp lệ.
                    </span>`)
                }

                if(!reason.val() || reason.val() == '')
                {
                    return reason.parent().append(`
                    <span class="text-xs text-red-600 dark:text-red-400 error">
                      Vui lòng điền lí do
                    </span>`)
                }

                $.ajax({
                    type: "POST",
                    url: `{{ route('admin.userBalance') }}?userid=${userid}&type=${type.val()}&amount=${amount.val()}&reason=${reason.val()}`,
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

            const dataList = [
                @foreach($data['users'] as $task)
                [
                    "{{ $task['id'] }}",
                    "{{ $task['name'] }}",
                    "{{ $task['email'] }}",
                    "{{ $task['balance'] ?? 0 }}",
                    "{{ $task['rentTotal'] ?? 0 }}",
                    "{{ $task['simCount'] ?? 0 }}",
                    "@if($task['profit'] == 1) Khoá API @else Hoạt Động @endif",
{{--                    "{{ $task['created_at'] }}",--}}
                    gridjs.html(`
                    <div class="flex items-center space-x-4 text-sm">
                                <button @click="openModal" data-user="{{ $task['id'] }}" class="editBtn flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Edit">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </button>
                                <button data-user="{{ $task['id'] }}" class="delete flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Delete">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                    `),
                ],
                @endforeach
            ];
            const grid = new gridjs.Grid({
                columns: [
                    { name: 'ID',
                        attributes: (cell) => {
                            if (cell) {
                                return {
                                    'data-user':cell
                                };
                            }
                    }},
                    "Tên",
                    "Email",
                    "Doanh Thu",
                    "Lượt Cho Thuê",
                    "Số Lượng Sim",
                    "Trạng Thái",
                    // "Ngày Tạo",
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
                    multiColumn: false
                },

                pagination: true
            }).render(document.getElementById("vendors"));

            function validateEmail($email) {
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                return emailReg.test( $email );
            }

            $('#accountEdit').click(function (e) {
                e.preventDefault();

                $('.error').each(function () {
                    $(this).remove()
                })

                const username = $('#username');
                const email = $('#email');
                const password = $('#password');

                if(!username.val() || username.val() == '')
                {
                    return username.parent().append(`
                    <span class="text-xs text-red-600 dark:text-red-400 error">
                      Vui lòng điền tên
                    </span>`)
                }

                if(!email.val() || !validateEmail(email.val()))
                {
                    return email.parent().append(`
                    <span class="text-xs text-red-600 dark:text-red-400 error">
                      Email không hợp lệ
                    </span>`)
                }

                toBePost = {
                    name: username.val(),
                    email: email.val(),
                    profit: parseInt($('#profitRate').val())
                }

                if(!password.val() == '')
                {
                    toBePost = Object.assign(toBePost, {
                        password: password.val()
                    })
                }

                $.ajax({
                    type: "POST",
                    url: `{{ route('admin.veryBadUserUpdate') }}`,
                    data: JSON.stringify({
                        userid: $(this).attr('data-user'),
                        data: toBePost
                    }),
                    contentType: "application/json",
                    dataType: 'json',
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

            $('#searchBtn').click(function (e) {
                e.preventDefault()
                const userid = $('#searchInput').val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.users') }}/"+userid,
                    cache: false,
                    success: function (data) {
                        fillToModal(data)
                    },
                    error: function (e) {
                    }
                });
            })
        });

        function fillToModal(data) {
            if(jQuery.isEmptyObject(data)) return alert('Không tìm thấy');
            $('#profitRate').val(data.profit);
            $('#email').val(data.email);
            $('#username').val(data.name);
            $('#userId').text(data.id);
            $('#accountEdit').attr('data-user', data.id);
            $('#balanceEdit').attr('data-user', data.id);
        }

        $(document).on('click','.editBtn',function(e){
            e.preventDefault()
            const userid = $(this).attr('data-user');

            $.ajax({
                type: "POST",
                url: "{{ route('admin.users') }}/"+userid,
                cache: false,
                success: function (data) {
                    fillToModal(data)
                },
                error: function (e) {
                }
            });
        })

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            var x = window.confirm("Bạn có chắc muốn xoá người dùng này?");
            if(!x) return false;
            $.ajax({
                type: "POST",
                url: `{{ route('admin.ban') }}`,
                data: JSON.stringify({
                    objType: 0,
                    objId: $(this).attr('data-user')
                }),
                contentType: "application/json",
                dataType: 'json',
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
    </script>
@endsection