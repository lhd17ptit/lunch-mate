<div class="mb-2">Tên khách hàng: {{ $orderServing->user->name ?? 'Chưa xác định' }}</div>
<div class="mb-2">Tầng: {{ $orderServing->user->floor->name ?? 'Chưa xác định' }}</div>
<div class="mb-2">Ngày đặt: {{ date('d/m/Y', strtotime($orderServing->created_at)) }}</div>
<div class="mb-2">Tổng tiền: {{ $orderServing->amount.',000 VND' }}</div>
<div class="mb-2">Món: 
    @php
        $foodCategoryName = '';
        $foodItemName = [];
    @endphp
    @foreach ($orderServing->orderServingFoodItems as $item)
        @if (!empty($item->foodItem->foodCategory) && empty($foodCategoryName) && $item->foodItem->foodCategory->key != 'com')
            {{ $item->foodItem->foodCategory->name }}
        @endif
        @php
            $foodCategoryName = true;
            $foodItemName[] = $item->foodItem->name ?? null;
        @endphp
    @endforeach
    @if (!empty($foodItemName))
        {{ implode(' + ', array_filter($foodItemName)) }}
    @endif
    @if (!empty($orderServing->note))
        &nbsp;( {{ $orderServing->note }})
    @endif
</div>