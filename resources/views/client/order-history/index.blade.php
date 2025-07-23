<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lịch sử đơn hàng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('client/assets/custom.css') }}">
</head>
<body>
    <div class="row p-0 m-0">
        <div class="col-1">&nbsp;</div>
        <div class="col-10">
            <h1 class="text-center mt-2 mb-4">Lịch sử đơn hàng</h1>

            <table class="table table-striped table-bordered data-table w-100" style="color:black">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Chi tiết</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($listOrders as $index => $order)
                        @php
                            $highlightRow = false;
                            $currentOrderCode = $order->order->order_code ?? null;
                            if($currentOrderCode == (request()->orderCode ?? null)) $highlightRow = true;
                            $foodCategoryName = '';
                            $foodItemName = [];
                        @endphp
                        @foreach ($order->orderServingFoodItems as $item)
                            @php
                                if (!empty($item->foodItem->foodCategory) && empty($foodCategoryName) && $item->foodItem->foodCategory->key != 'com') {
                                    $foodCategoryName = $item->foodItem->foodCategory->name ?? '';
                                }
                                $foodItemName[] = $item->foodItem->name ?? null;
                            @endphp
                        @endforeach
                        <tr 
                            @if ($highlightRow)
                                class="glow-bg"
                            @endif
                        >
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $order->user->name ?? 'Chưa xác định' }}</td>
                            <td>{{ $order->created_at->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}</td>
                            <td>{{ $order->amount.',000' }}</td>
                            <td>
                                @if (!empty($foodItemName))
                                    {{ (!empty($foodCategoryName) ? $foodCategoryName . ' ' : '') . implode(' + ', array_filter($foodItemName)) }}
                                @else
                                    Chưa xác định
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-primary clone-serving" data-order-serving-id="{{ $order->orderServingFoodItems->pluck('food_item_id') }}">Copy</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-1">&nbsp;</div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('admin/assets/js/toastr.js') }}"></script>

    <script>
        $('.clone-serving').on('click', function() {
            foodItemIds = $(this).data('order-serving-id');
            url = new URL("{{ route('home') }}");
            foodItemIds.forEach(id => url.searchParams.append('selectedItemIds[]', id));
            window.location.href = url.toString();
        })

        $(document).ready(function(){
            $target = $('.glow-bg').first();
            if ($target.length) {
                $('html, body').animate({
                scrollTop: $target.offset().top - ($(window).height() / 2) + ($target.outerHeight() / 2)
                }, 1000); // 1000ms
            }
        })
    </script>

</body>
</html>