<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LunchMate - Đặt Cơm Văn Phòng Nhanh & Ngon</title>
    <meta name="description" content="Đặt cơm văn phòng tại tòa nhà Sông Đà nhanh chóng, đa dạng món ăn, giao cơm tận nơi, đảm bảo nóng hổi và ngon miệng mỗi ngày.">
    <meta name="keywords" content="đặt cơm, cơm văn phòng, Sông Đà, giao cơm, cơm trưa văn phòng, đặt cơm nhanh, lunchmate, Mễ Trì, Phạm Hùng">
    <meta name="author" content="Lunch Mate">
    <meta name="robots" content="index, follow">

    <meta name="google-site-verification" content="bkb8Ilc2n-HrbdTGx2btcD9g2uuYj7GdiAwa-Wfqin0" />
    <meta property="og:title" content="Đặt Cơm Văn Phòng Tòa Nhà Sông Đà - Ngon, Nhanh, Tiện Lợi">
    <meta property="og:description" content="Dịch vụ giao cơm văn phòng chuyên nghiệp tại tòa nhà Sông Đà. Đặt cơm nhanh - Giao đúng giờ - Món ăn đa dạng.">
    <meta property="og:image" content="{{ asset('admin/assets/images/logos/new-banner.png') }}">
    <meta property="og:url" content="https://lunchmate.online/">
    <meta property="og:type" content="website">

    <link rel="icon" type="image/png" href="{{ asset('admin/assets/favicon_io/favicon-32x32.png') }}" sizes="32x32">
    <link rel="canonical" href="https://lunchmate.online/">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('client/assets/custom.css') }}">
</head>
<body>
    <h1 class="visually-hidden">LunchMate - Đặt Cơm Văn Phòng Tòa Nhà Sông Đà</h1>
    @if (empty($menu))
        <img src="{{ asset('admin/assets/images/backgrounds/coming-soon-page.webp') }}" alt="logo" style="width: 100%; height: 100vh; object-fit: cover;">
    @else
        <div class="row p-0 m-0 mb-5">
            <div class="col-md-6">
                <div class="title-menu">THỰC ĐƠN HÔM NAY</div>
                <div class="block-menu row">
                    {{-- <div class="col-md-1">&nbsp;</div> --}}
                    <div class="col-md-12">
                        {{-- @if (!empty($menu->foodCategories))
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
                        </div> --}}
                        <div class="choose-food mt-3">
                            <div class="form-check mt-0">
                                Người đặt: <br>

                                <div class="row mt-3 mb-2" style="font-size: 15px; font-weight: 400;">
                                    <div class="col-md-6">
                                        <input type="radio" name="type_user" value="1" checked>
                                        <label for="type_user">Đã có tài khoản</label>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="radio" name="type_user" value="2">
                                        <label for="type_user">Người mới</label>
                                    </div>
                                </div>
        
                                <select name="user_id" id="user_id" class="form-control mt-4">
                                    <option value="">Chọn người đặt</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>

                                <div class="row new-user d-none" style="font-size: 15px; font-weight: 400;">
                                    <div class="col-md-6 mb-2">
                                        <input type="text" class="form-control" name="name" id="user_name" placeholder="Tên người đặt">
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-control" name="floor" id="floor_id">
                                            <option value="">Chọn tầng</option>
                                            @foreach ($floors as $floor)
                                                <option value="{{ $floor->id }}">{{ $floor->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <p class="text-center">-----------------------------------------------</p>
                            @if (!empty($menu->foodCategories))
                                @foreach ($menu->foodCategories as $foodCategory)
                                    @if (in_array($foodCategory->id, $foodCategories))
                                        <div class="form-check">
                                            <label class="form-check-label" for="food-category-{{ $foodCategory->id }}">
                                                {{ $foodCategory->name }} <span style="font-size: 14px; font-weight: 400">{{ $foodCategory->key != 'com' ? '(Suất '.$foodCategory->price.',000 VND)' : '' }}</span>
                                            </label>
                                        </div>
                                        <div class="row food-item food-category-{{ $foodCategory->id }}">
                                            @php
                                                $typeOne = false;
                                                $typeTwo = false;
                                                $typeThree = false;
                                            @endphp
                                            @foreach ($foodCategory->foods as $food)
                                                @if (in_array($food->id, $foodItems))
                                                    @if ($food->type == 1 && empty($typeOne))
                                                        <div class="text-center col-10 mt-3">Món chính(+10k)</div>
                                                        @php
                                                            $typeOne = true;
                                                        @endphp
                                                    @elseif ($food->type == 2 && empty($typeTwo))
                                                        <div class="text-center col-10 mt-3">Món phụ(+5k)</div>
                                                        @php
                                                            $typeTwo = true;
                                                        @endphp
                                                    @elseif ($food->type == 3 && empty($typeThree))
                                                        <div class="text-center col-10 mt-3">Món rau(+5k)</div>
                                                        @php
                                                            $typeThree = true;
                                                        @endphp
                                                    @endif
                                                    <div class="form-check-item {{ $foodCategory->key != 'com' ? 'col-md-3 col-6' : 'col-md-5 col-6' }}">
                                                        <input class="form-check-input ck-food-item item-food-category-{{ $foodCategory->id }}" type="checkbox" value="{{ $food->id }}" id="food-item-{{ $food->id }}" data-id="{{ $food->id }}"
                                                            @if (in_array($food->id, $selectedItemIds))
                                                                checked
                                                            @endif
                                                        >
                                                        <label class="form-check-label" for="food-item-{{ $food->id }}">
                                                            {{ $food->name }} {{ $food->type == 4 ? '(+5k)' : '' }}
                                                        </label>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                            <p class="text-center mt-5 mb-1">-----------------------------------------------</p>
                            <div class="choose-food pt-0 pb-0 form-note">
                                <label class="mb-2" for="note" style="font-weight: 600; font-size:18px">Ghi chú:</label>
                                <input type="text" class="form-control" name="note" placeholder="Nhập ghi chú" class="note" id="note">
                            </div>
                        </div>
                        <div class="mt-3 text-total-order">Số tiền cần thanh toán: <span id="total">0</span> VND</div>

                        <div class="d-flex justify-content-center mt-3 mb-4">
                            <button type="button" class="btn btn-primary" id="save_order">HOÀN THÀNH SUẤT</button>
                        </div>
                    </div>

                    {{-- <div class="col-md-1">&nbsp;</div> --}}
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
                                        @if (!empty($item['note']))
                                            ( {{ $item['note'] }})
                                        @endif
                                    </span><br>
                                    <div class="order-price"> = {{ $item['total'] }},000 VND</div>
                                </div>
                                <div class="order-delete" data-id="{{ $key }}">
                                    <i class="fa fa-trash" style="cursor: pointer;"></i>
                                </div>
                            </div>
                            @endforeach
                            <div class="total-order d-flex flex-column align-items-center">
                                @php
                                    $total = array_sum(array_column($cart, 'total'));
                                @endphp
                                <div class="mt-5 mb-3 fs-5">
                                    <b>Tip thêm cho nhà phát triển</b>
                                </div>
                                <div class="btn-group tip-options w-100" role="group">
                                    <input type="radio" class="btn-check select-tip" name="tip" id="tip-0" autocomplete="off" value="0" checked>
                                    <label class="btn btn-outline-success" for="tip-0">0đ</label>

                                    <input type="radio" class="btn-check select-tip" name="tip" id="tip-500" autocomplete="off" value="0.5">
                                    <label class="btn btn-outline-success" for="tip-500">500đ</label>

                                    <input type="radio" class="btn-check select-tip" name="tip" id="tip-1000" autocomplete="off" value="1">
                                    <label class="btn btn-outline-success" for="tip-1000">1000đ</label>

                                    <input type="radio" class="btn-check select-tip" name="tip" id="tip-2000" autocomplete="off" value="2">
                                    <label class="btn btn-outline-success" for="tip-2000">2000đ</label>
                                </div>
                                <div class="total-order-text mt-3 fs-5"><b>Tổng tiền: <span class="total-order-amount" data-total="{{$total}}">{{ number_format($total * 1000, 0, ',') }}</span> VND</b></div>
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
                <div class="mt-3 text-history-order"><a href="{{ route('list-order') }}" target="_blank">Lịch sử đặt đơn <i class="fa fa-angle-double-right" aria-hidden="true"></i></a></div>
                <div class="w-100 d-flex justify-content-center mt-5">
                    <img src="{{ asset('client/assets/images/lunchdog.png') }}">
                </div>
            </div>
        </div>
		{{-- hidden form to submit reroute to payment --}}
		<form id="checkout-form" action="{{ route('checkout-order') }}" method="POST" style="display:none;">
			@csrf
            <input type="hidden" id="tip" name="tip" value="0">
		</form>
    @endif
    <div id="news_tip"></div>


    <!-- Modal -->
    {{-- <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
    </div> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('admin/assets/js/toastr.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#user_id').select2();
            checkTypeUser();

            $.ajax({
                type: 'GET',
                url: '{{ route('get-news-tip') }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                },
                success: function(data) {
                    $('#news_tip').html(data.data.view);
                },
                error: function(error) {                            
                    $('#news_tip').html('')
                }
            });

            $('.btn-order-now').click(function() {
                $('.ck-food-item').prop('checked', false);
                $('#total').html(0);
                $('#user_id').val('');
                $('#exampleModalCenter').modal('show');
            });

            function updateServingTotal(e){
                var items = [];
                const checkbox = e;

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
                            toastr.error(error.responseJSON.message);
                            $(checkbox).prop('checked', false);
                        }
                    });
                } else {
                    $('#total').html('0');
                }
            }

            $(document).on('change', '.ck-food-item', function() {
                updateServingTotal(this)
            });

            updateServingTotal(null);

            $(document).on('click', '#save_order', function() {
                var items = [];
                var user_id = $('#user_id').val();
                var user_name = $('#user_name').val();
                var floor_id = $('#floor_id').val();
                var type_user = $('input[name="type_user"]:checked').val();
                var note = $('input[name="note"]').val();
            
                if (type_user == 2) {
                    if (user_name == '') {
                        toastr.error('Người đặt không được để trống');
                        return;
                    }
                    if (floor_id == '') {
                        toastr.error('Tầng không được để trống');
                        return;
                    }
                } else {
                    if (user_id == '') {
                        toastr.error('Vui lòng chọn người đặt');
                        return;
                    }
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
                            type_user: type_user,
                            user_id: user_id,
                            user_name: user_name,
                            floor_id: floor_id,
                            note: note
                        },
                        success: function(data) {
                            $('#order-list').html(data.data.view);
                            $('#user_id').val(null).trigger('change');
                            $('.ck-food-item').prop('checked', false);
                            $('#total').html('0');

                            $('input[name="type_user"][value="1"]').prop('checked', true);
                            $('#user_id').next('.select2-container').removeClass('d-none');
                            $('.new-user').addClass('d-none');
                            $('#user_name').val('');
                            $('#floor_id').val('');
                            $('#note').val('');
                        },
                        error: function(error) {                            
                            toastr.error(error.responseJSON.message);
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
                        toastr.error(error.responseJSON.message);
                    }
                });
            });
        });

        $(document).on('click', '.btn-checkout', function() {
            var count = $('#count-item').val();
            if (count == 0) {
                toastr.error('Vui lòng chọn món');
                return;
            }
            $('#checkout-form').submit();
        });

        $(document).on('change', 'input[name="type_user"]', function() {
            checkTypeUser();
        });

        function updateTipValue(){
            $('#tip').val($('.select-tip:checked').val())
        }

        // update total on changing tip
        $(document).on('change', '.select-tip', function(){
            totalValue = $('.total-order-amount').data('total') * 1000
            selectedTipAmount = $('.select-tip:checked').val() * 1000
            newTotalAmount = totalValue + selectedTipAmount
            $('.total-order-amount').text(newTotalAmount.toLocaleString('en-US'))
            updateTipValue()
        });

        $(document).ready(function() {
            $('.select-tip').change()
            updateTipValue();
        })

        function checkTypeUser() {
            var type_user = $('input[name="type_user"]:checked').val();
            
            if (type_user == 2) {
                $('#user_id').next('.select2-container').addClass('d-none');
                $('.new-user').removeClass('d-none');
            } else {
                $('#user_id').next('.select2-container').removeClass('d-none');
                $('.new-user').addClass('d-none');
            }
        }
    </script>
</body>
</html>