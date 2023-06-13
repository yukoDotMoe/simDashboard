@extends('layouts.app')

@section('title')
    Account Settings
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Account Settings <p class="text-xs text-gray-600 dark:text-gray-200">
            Allow you to manage personal information and rental preferences.
        </p></h2>
    <a class="flex items-center justify-between p-4 mb-6 text-sm font-semibold text-purple-100 bg-purple-600 rounded-lg shadow-md focus:outline-none focus:shadow-outline-purple">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"></path>
            </svg>
            <span>Your token: <strong id="copyToken">{{ Auth::user()->api_token }}</strong> </span>
        </div>
        <button @click="openModal">| Click here to reset</button>
    </a>

    <div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <p class="text-sm text-gray-600 dark:text-gray-400">
        <form action="{{ route('vendor.accounts.changePass') }}" method="POST">
            @csrf
            <label class="block text-sm mt-2 mb-4">
                <span class="text-gray-700 dark:text-gray-400">Current Password</span>
                <input type="password" name="current_password" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" required>
            </label>

            <label class="block text-sm mt-2 mb-4">
                <span class="text-gray-700 dark:text-gray-400">New Password</span>
                <input type="password" name="new_password" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" required>
            </label>

            <label class="block text-sm mt-2 mb-4">
                <span class="text-gray-700 dark:text-gray-400">Re-type New Password</span>
                <input type="password" name="new_password_confirmation" class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input" required>
            </label>

            <button id="createService" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                Change Password
            </button>
        </form>
        </p>
    </div>

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
            <!-- Remove header if you don't want a close icon. Use modal body to place modal tile. -->

            <!-- Modal body -->
            <div class="mt-4 mb-6">
                <!-- Modal title -->
                <p
                        class="mb-2 text-lg font-semibold text-gray-700 dark:text-gray-300"
                >
                    Reset your API token?
                </p>
                <!-- Modal description -->
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    This option is only needed when you think your token got problem or someone stole it, if not please don't use it. Sincerely, the dev team.
                </p>
            </div>
            <footer
                    class="flex flex-col items-center justify-end px-6 py-3 -mx-6 -mb-4 space-y-4 sm:space-y-0 sm:space-x-6 sm:flex-row bg-gray-50 dark:bg-gray-800"
            >
                <button id="close"
                        @click="closeModal"
                        class="w-full px-5 py-3 text-sm font-medium leading-5 text-white text-gray-700 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 sm:px-4 sm:py-2 sm:w-auto active:bg-transparent hover:border-gray-500 focus:border-gray-500 active:text-gray-500 focus:outline-none focus:shadow-outline-gray"
                >
                    Cancel
                </button>
                <button id="resetToken"
                        class="w-full px-5 py-3 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg sm:w-auto sm:px-4 sm:py-2 active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
                >
                    Accept
                </button>
            </footer>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('#resetToken').click(function (e) {
            e.preventDefault()
            $.ajax({
                type: "POST",
                url: "{{ route('vendor.resetToken') }}",
                cache: false,
                success: function (data) {
                    if(data.status > 200)
                    {
                        return vt.error(data.message, {
                            title: "Error",
                            position: "top-right",
                        })
                    }
                    vt.success("Your token has been reset", {
                        title: "Successfully",
                        position: "top-right",
                    })
                    $('#close').click()
                    $('#copyToken').text(data.data.token)
                },
                error: function (e) {
                    return vt.error(e, {
                        title: "Error",
                        position: "top-right",
                    })
                }
            });
        })
    </script>
@endsection