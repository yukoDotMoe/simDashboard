@extends('layouts.app')

@section('title')
    Danh Sách Người Dùng
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Quản Lý Khách Hàng </h2>
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
                        <span class="text-gray-700 dark:text-gray-400">Username</span>
                        <input id="username" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"/>
                    </label>

                    <label class="mb-4 block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Mật khẩu</span>
                        <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                               id="password" type="text" />
                    </label>

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
                            <input class="block w-full pr-20 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="API Token" name="token" id="token" />
                            <button id="randompass" class="absolute inset-y-0 right-0 px-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-r-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                                Ngẫu nhiên
                            </button>
                        </div>

                    </div>

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
                <input class="block w-full pr-20 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input" placeholder="Tìm theo email" id="searchInput" />
                <button id="searchBtn" @click="openModal" class="absolute inset-y-0 right-0 px-4 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-r-md active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Tìm kiếm
                </button>
            </div>

        </div>
    </div>

    <div class="w-full mb-8 overflow-hidden rounded-lg">
        <div class="w-full overflow-x-auto">
            <div id="users"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $( document ).ready(function() {
            const dataList = [
                @foreach($data['users'] as $task)
                [
                    "{{ $task['id'] }}",
                    "{{ $task['username'] }}",
                    "{{ number_format($task['balance'], 0, '', ',') ?? 0 }}",
                    "{{ $task['totalRent'] ?? 0 }}",
                    "@if($task['ban'] == 1) Khoá @else Hoạt Động @endif",
                    "{{ $task['created_at'] }}",
                    gridjs.html(`
                            <div class="flex items-center space-x-4 text-sm">
                                <button @click="openModal" data-user="{{ $task['id'] }}" class="editBtn flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Edit">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                    </svg>
                                </button>
                                <a href="{{ route('admin.user.history', $task['id']) }}" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Edit">
                                    <svg class="w-5 h-5"  fill="currentColor" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"></path>
</svg>
                                </a>
                                @if($task['ban'] == 1)
                                    <button data-user="{{ $task['id'] }}" class="restore flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Delete">
                                        <svg class="w-5 h-5" stroke="currentColor" fill="white" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"></path>
                                        </svg>
                                    </button>
                                @else
                                    <button data-user="{{ $task['id'] }}" class="delete flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Delete">
                                        <svg class="w-5 h-5" stroke="currentColor" fill="white" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"></path>
                                        </svg>
                                    </button>
                                @endif
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
                    "Username",
                    "Số dư",
                    "Sim đã thuê",
                    "Trạng Thái",
                    "Ngày Tạo",
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
            }).render(document.getElementById("users"));
            
            $('#randompass').click(function (e) {
                e.preventDefault()

                var length = 80,
                    charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
                    retVal = "";
                for (var i = 0, n = charset.length; i < length; ++i) {
                    retVal += charset.charAt(Math.floor(Math.random() * n));
                }
                $('input[name="token"]').val(retVal)
            })



            function validateEmail($email) {
                var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
                return emailReg.test( $email );
            }

            $('#accountEdit').click(function (e) {
                e.preventDefault();
                $('.error').each(function () {
                    $(this).remove()
                })
                const userid = $(this).attr('data-user');
                const username = $('#username');
                const password = $('#password');
                const token = $('#token');

                if(!token.val() || token.val() == '')
                {
                    return token.parent().append(`
                    <span class="text-xs text-red-600 dark:text-red-400 error">
                      Vui lòng điền token
                    </span>`)
                }

                if(!username.val() || username.val() == '')
                {
                    return username.parent().append(`
                    <span class="text-xs text-red-600 dark:text-red-400 error">
                      Vui lòng điền tên
                    </span>`)
                }


                toBePost = {
                    username: username.val(),
                    api_token: token.val(),
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
                $('#username').val(data.username);
                $('#userId').text(data.id);
                $('#token').val(data.api_token);
                $('#balanceEdit').attr('data-user', data.id);
                $('#accountEdit').attr('data-user', data.id);
            }
        
        $(document).on('click','.editBtn',function(e){
                            e.preventDefault()
                const userid = $(this).attr('data-user');

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.users') }}/"+userid,
                    cache: false,
                    success: function (data) {
                        console.log(data)
                        fillToModal(data)
                    },
                    error: function (e) {
                    }
                });
        })

        $(document).on('click','.delete',function(e){
            e.preventDefault();
            var x = window.confirm("Bạn có chắc muốn khoá người dùng này?");
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
                    if(data.status > 200)
                    {
                        return vt.error(data.message, {
                            title: "Lỗi",
                            position: "top-right",
                        })
                    }
                    vt.success("Đã khoá người dùng thành công", {
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

        $(document).on('click','.restore',function(e){
            e.preventDefault();
            var x = window.confirm("Bạn có chắc muốn bỏ khoá người dùng này?");
            if(!x) return false;
            $.ajax({
                type: "POST",
                url: `{{ route('admin.ban') }}`,
                data: JSON.stringify({
                    objType: 0,
                    objId: $(this).attr('data-user'),
                    unban: 1
                }),
                contentType: "application/json",
                dataType: 'json',
                cache: false,
                success: function (data) {
                    if(data.status > 200)
                    {
                        return vt.error(data.message, {
                            title: "Lỗi",
                            position: "top-right",
                        })
                    }
                    vt.success("Đã hồi sinh người dùng thành công", {
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