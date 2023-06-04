@extends('layouts.app')

@section('title')
    Tạo Dịch Vụ
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Tạo dịch vụ mới </h2>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <form>
                <label class="block text-sm mt-2 mb-4">
                    <span class="text-gray-700 dark:text-gray-400">Tên Dịch Vụ</span>
                    <input id="serviceName" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" required>
                </label>

                <label class="block text-sm mb-4">
                    <span class="text-gray-700 dark:text-gray-400">Giá</span>
                    <input id="price" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" type="number" min="1000" required>
                </label>

                <label class="mb-4 block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Khoá SIM sau X lượt dùng</span>
                    <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                           id="useCount" type="number" placeholder="-1 để không giới hạn lượt dùng" value="-1" required />
                    <span class="text-xs text-gray-600 dark:text-gray-400">
                              Nhập <strong>-1</strong> để không giới khoá
                            </span>
                </label>

                <label class="mb-4 block text-sm hidden" id="cooldown-div">
                    <span class="text-gray-700 dark:text-gray-400">Mở lại SIM sau khi bị khoá (giờ)</span>
                    <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                           id="cooldown" min="1" value="1" type="number" required />
                </label>

                <label class="block mt-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Regex lấy code</span>
                    <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                           id="structure" type="text" required />
                </label>

                <label class="block mt-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Kiểm tra cấu trúc</span>
                    <input class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                           id="checkValid" type="text" required />
                </label>

                <button id="createService" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Tạo dịch vụ
                </button>
            </form>
        </p>
    </div>
@endsection

@section('js')
    <script>
        $( document ).ready(function() {
            $('#createService').click(function (e) {
                e.preventDefault();
                const name = $('#serviceName').val();
                const price = $('#price').val();
                const limit = $('#useCount').val();
                const cooldown = $('#cooldown').val();
                const structure = $('#structure').val();
                const checkValid = $('#checkValid').val();


                $.ajax({
                    type: "POST",
                    url: `{{ route('admin.createServicePost') }}?name=${name}&price=${price}&limit=${parseInt(limit)}&cooldown=${parseInt(cooldown)}&structure=${structure}&valid=${checkValid}`,
                    cache: false,
                    success: function (data) {
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
                        window.location.href = "{{ route('admin.services') }}";
                    },
                    error: function (e) {
                        return vt.error(e, {
                            title: "Lỗi",
                            position: "top-right",
                        })
                    }
                });
            })

            $('#useCount').on('keyup change', function(){
                if($(this).val() >= 1 ) {
                    $('#cooldown-div').removeClass('hidden')
                }else{
                    if($(this).val() < -1) $(this).val(-1)
                    $('#cooldown-div').addClass('hidden')
                }
            })
        });
    </script>
@endsection