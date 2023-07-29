@extends('layouts.app')

@section('title')
    Danh Sách Người Dùng
@endsection

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"> Đang xem lịch sử của người dùng:  {{ $user->name }}</h2>
    <div class="w-full mb-8 overflow-hidden rounded-lg">
        <div class="w-full overflow-x-auto">
            <div id="payments"></div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            function formatStatus(status) {
                value = ``;
                switch (status) {
                    case 0:
                        value = `Hoàn tiền`
                        break;
                    case 1:
                        value = `Mua`
                        break;
                    case 2:
                        value = `Giữ tiền`
                        break;
                    case 3:
                        value = `Nạp tiền`
                        break;
                    case 3:
                        value = `Trừ tiền`
                        break;
                }
                return value;
            }

            let transactions = [
                    @foreach($transactions as $tran)
                [
                    "{{ $tran['uniqueId'] }}",
                    "{{ $tran['serviceName'] }}",
                    "{{ str_contains($tran['activityId'], 'adminAction') ? 'Admin' : $tran['activityId']}}",
                    formatStatus({{ $tran['status'] }}),
                    "{{ $tran['totalChange'] }}",
                    "{{ $tran['oldBalance'] }}",
                    "{{ $tran['newBalance'] }}",
                    "{{ $tran['created_at'] }}",
                ],
                @endforeach
            ];
            new gridjs.Grid({
                columns: ["ID",{
                    name: "Dịch vụ",
                    id: "service"
                },{
                    name: "Request",
                    id: "Request"
                },{
                    name: "Type",
                    id: "status"
                },{
                    name: "Amount",
                    id: "change"
                },{
                    name: "Old",
                    id: "oldBal"
                },{
                    name: "New",
                    id: "newBal"
                },{
                    name: "Time",
                    id: "date"
                }],
                data: transactions,
                search: true,
                sort: {
                    multiColumn: true
                },
                pagination: {
                    limit: 30,
                    summary: false
                }
            }).render(document.getElementById("payments"));
        })
    </script>
@endsection