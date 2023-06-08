@extends('layouts.app')

@section('title')
    Vendor's Dashboard
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Sim List </h2>
    <div class="w-full mb-8 overflow-hidden">
        <div class="w-full overflow-x-auto">
            <div id="sims" class="text-center"><i class="fa-solid fa-circle-notch fa-spin"></i></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            let dataList = [
                @foreach($data as $task)
                    ["{{ $task['phone'] }}", "{{ $task['status'] }}", "{{ $task['success'] }}", "{{ $task['failed'] }}", "{{ $task['updated_at'] }}"],
                @endforeach
            ];

            $('#sims').html('')
            new gridjs.Grid({
                columns: ["Phone", "Status", "Success", "Failed", "Last Update"],
                data: dataList,
                // search: true,
                sort: {
                    multiColumn: false
                },
                // pagination: true
            }).render(document.getElementById("sims"));

            console.log( "\u001b[1;31m You shouldn't open this panel you know. But welcome, {{ Auth::user()->email }}" );
        })
    </script>
@endsection