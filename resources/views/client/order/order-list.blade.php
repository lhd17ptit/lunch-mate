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
        @php
            $tip = $tip ?? 0;
        @endphp
        <div class="btn-group tip-options w-100" role="group">
            <input type="radio" class="btn-check select-tip" name="tip" id="tip-0" autocomplete="off" value="0" {{ $tip == 0 ? 'checked' : ''}}>
            <label class="btn btn-outline-success" for="tip-0">0đ</label>

            <input type="radio" class="btn-check select-tip" name="tip" id="tip-500" autocomplete="off" value="0.5" {{ $tip == 0.5 ? 'checked' : ''}}>
            <label class="btn btn-outline-success" for="tip-500">500đ</label>

            <input type="radio" class="btn-check select-tip" name="tip" id="tip-1000" autocomplete="off" value="1" {{ $tip == 1 ? 'checked' : ''}}>
            <label class="btn btn-outline-success" for="tip-1000">1000đ</label>

            <input type="radio" class="btn-check select-tip" name="tip" id="tip-2000" autocomplete="off" value="2" {{ $tip == 2 ? 'checked' : ''}}>
            <label class="btn btn-outline-success" for="tip-2000">2000đ</label>
        </div>
        <div class="total-order-text mt-3 fs-5"><b>Tổng tiền: <span class="total-order-amount" data-total="{{$total + $tip}}">{{ number_format(($total + $tip) * 1000, 0, ',') }}</span> VND</b></div>
    </div>
@else
    Không có đơn đặt hôm nay
@endif
<input type="hidden" id="count-item" value="{{ count($cart) }}">