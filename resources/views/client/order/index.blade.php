<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link rel="stylesheet" href="{{ asset('client/assets/custom.css') }}">
</head>
<body>
    @if (empty($menu))
        <img src="{{ asset('admin/assets/images/backgrounds/coming-soon-page.webp') }}" alt="logo" style="width: 100%; height: 100vh; object-fit: cover;">
    @else
        <div class="row p-0 m-0">
            <div class="col-md-6">
                <div class="title-menu">THỰC ĐƠN HÔM NAY</div>
                <div class="block-menu row">
                    <div class="col-md-2">&nbsp;</div>
                    <div class="col-md-8">
                        @if (!empty($menu->foodCategories))
                            @foreach ($menu->foodCategories as $foodCategory)
                                @if (in_array($foodCategory->id, $foodCategories))
                                    <div class="food-category">
                                        {{ $foodCategory->name }}
                                    </div>
                                    <div class="sub-title">
                                        @if ($foodCategory->key != 'com')
                                            (Suất {{ $foodCategory->price.',000 VND' }})
                                        @endif
                                    </div>
                                    <div class="row food-item mt-2">
                                        @php
                                            $typeOne = false;
                                            $typeTwo = false;
                                            $typeThree = false;
                                        @endphp
                                        @foreach ($foodCategory->foods as $food)
                                            @if (in_array($food->id, $foodItems))
                                                @if ($food->type == 1 && empty($typeOne))
                                                    <div class="text-center col-12 mt-2 mb-1" style="font-size: 14px; text-decoration: underline; font-weight: 500;">Món chính</div>
                                                    @php
                                                        $typeOne = true;
                                                    @endphp
                                                @elseif ($food->type == 2 && empty($typeTwo))
                                                    <div class="text-center col-12 mt-3 mb-1" style="font-size: 14px; text-decoration: underline">Món phụ</div>
                                                    @php
                                                        $typeTwo = true;
                                                    @endphp
                                                @elseif ($food->type == 3 && empty($typeThree))
                                                    <div class="text-center col-12 mt-3 mb-1" style="font-size: 14px; text-decoration: underline">Món rau</div>
                                                    @php
                                                        $typeThree = true;
                                                    @endphp
                                                @endif
                                                <div class="food-item {{ $foodCategory->key != 'com' ? 'col-md-4 col-6' : 'col-md-6 col-6' }}">
                                                    <div class="food-name">+ {{ $food->name }}</div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    @if (!empty($foodCategory->note))
                                        <div style="font-size: 13px; margin-top: 10px">
                                            Note: {{ $foodCategory->note }}
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @endif

                        <div class="d-flex justify-content-center mt-5">
                            <div class="btn-order-now" data-toggle="modal" data-target="#exampleModalCenter">CHỌN MÓN</div>
                        </div>
                    </div>
                    <div class="col-md-2">&nbsp;</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="title-menu">ĐƠN ĐẶT HÔM NAY</div>
                <div class="block-order">
                    <div class="order-list" id="order-list">
                        @if (!empty($cart))
                            @foreach ($cart as $key => $item)
                            <div class="order position-relative">
                                <div class="user-name">{{ $item['user_name'] }}</div>
                                <div class="order-items">
                                    <span class="order-food-category">{{ $item['name_category'] }}</span>: 
                                    <span class="order-item">
                                        @php
                                            $names = array_column($item['items'], 'title');
                                        @endphp
                                        {{ implode(' + ', $names) }}
                                    </span>
                                    <span class="order-price"> = {{ $item['total'] }},000 VND</span>
                                </div>
                                <div class="order-delete" data-id="{{ $key }}">
                                    <i class="fa fa-trash" style="cursor: pointer;"></i>
                                </div>
                            </div>
                            @endforeach
                            <div class="total-order">
                                @php
                                    $total = array_sum(array_column($cart, 'total'));
                                @endphp
                                <span class="total-order-text">Tổng tiền: {{ $total }},000 VND</span>
                            </div> 
                        @else
                            Không có đơn đặt hôm nay
                        @endif
                        <input type="hidden" id="count-item" value="{{ count($cart) }}">
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <div class="btn-checkout">ĐẶT ĐƠN</div>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <!-- Modal -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenterTitle">Chọn món</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <div class="modal-body">
                <div class="choose-food mt-3">
                    <div class="form-check mt-0">
                        Người đặt: 

                        <select name="user_id" id="user_id" class="form-control mt-2">
                            <option value="">Chọn người đặt</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-center">-------------------------------------------------------</p>
                    @if (!empty($menu->foodCategories))
                        @foreach ($menu->foodCategories as $foodCategory)
                            @if (in_array($foodCategory->id, $foodCategories))
                                <div class="form-check">
                                    <label class="form-check-label" for="food-category-{{ $foodCategory->id }}">
                                        {{ $foodCategory->name }}
                                    </label>
                                </div>
                                <div class="row food-item food-category-{{ $foodCategory->id }}">
                                    @php
                                        $typeOne = false;
                                        $typeTwo = false;
                                        $typeThree = false;
                                    @endphp
                                    @foreach ($foodCategory->foods as $food)
                                        @if ($food->type == 1 && empty($typeOne))
                                            <div class="text-center col-10 mt-3">Món chính</div>
                                            @php
                                                $typeOne = true;
                                            @endphp
                                        @elseif ($food->type == 2 && empty($typeTwo))
                                            <div class="text-center col-10 mt-3">Món phụ</div>
                                            @php
                                                $typeTwo = true;
                                            @endphp
                                        @elseif ($food->type == 3 && empty($typeThree))
                                            <div class="text-center col-10 mt-3">Món rau</div>
                                            @php
                                                $typeThree = true;
                                            @endphp
                                        @endif
                                        <div class="form-check-item {{ $foodCategory->key != 'com' ? 'col-md-3 col-6' : 'col-md-5 col-6' }}">
                                            <input class="form-check-input ck-food-item item-food-category-{{ $foodCategory->id }}" type="checkbox" value="{{ $food->id }}" id="food-item-{{ $food->id }}" data-id="{{ $food->id }}">
                                            <label class="form-check-label" for="food-item-{{ $food->id }}">
                                                {{ $food->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
                <div class="mt-3" style="font-size: 18px; font-weight: 600;">Số tiền cần thanh toán: <span id="total">0</span> VND</div>
            </div>
            <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary" id="save_order">Lưu</button>
            </div>
        </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.btn-order-now').click(function() {
                $('.ck-food-item').prop('checked', false);
                $('#total').html(0);
                $('#user_id').val('');
            });

            $(document).on('change', '.ck-food-item', function() {
                var items = [];
                const checkbox = this;

                $('.ck-food-item').each(function() {
                    if ($(this).prop('checked')) {
                        var id = $(this).val();
                        items.push(id);
                    }
                });                

                if (items.length > 0) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('get-total-order') }}',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            items: items,
                        },
                        success: function(data) {
                            $('#total').html(data.total + ',000');
                        },
                        error: function(error) {                            
                            alert(error.responseJSON.message);
                            $(checkbox).prop('checked', false);
                        }
                    });
                }
            });

            $(document).on('click', '#save_order', function() {
                var items = [];
                var user_id = $('#user_id').val();

                if (user_id == '') {
                    alert('Vui lòng chọn người đặt');
                    return;
                }

                $('.ck-food-item').each(function() {
                    if ($(this).prop('checked')) {
                        var id = $(this).val();
                        items.push(id);
                    }
                });                

                if (items.length > 0) {
                    $.ajax({
                        type: 'POST',
                        url: '{{ route('add-to-order') }}',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            items: items,
                            user_id: user_id,
                        },
                        success: function(data) {
                            $('#order-list').html(data.data.view);
                            $('#exampleModalCenter').modal('hide');
                        },
                        error: function(error) {                            
                            alert(error.responseJSON.message);
                            $(checkbox).prop('checked', false);
                        }
                    });
                }
            });

            $(document).on('click', '.order-delete', function() {
                var id = $(this).data('id');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('remove-item-to-order') }}',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id,
                    },
                    success: function(data) {
                        $('#order-list').html(data.data.view);
                    },
                    error: function(error) {                            
                        alert(error.responseJSON.message);
                    }
                });
            });
        });

        $(document).on('click', '.btn-checkout', function() {
            var count = $('#count-item').val();
            if (count == 0) {
                alert('Vui lòng chọn món');
                return;
            }

            $.ajax({
                type: 'POST',
                url: '{{ route('checkout-order') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    alert('Đặt đơn thành công');
                },
                error: function(error) {                            
                    alert('Đặt đơn thất bại');
                }
            });
        });
    </script>
</body>
</html>