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