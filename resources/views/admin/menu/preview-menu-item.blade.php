@if (!empty($menu->foodCategories))
    @foreach ($menu->foodCategories as $foodCategory)
        @if (in_array($foodCategory->id, $foodCategories))
            <div class="form-check">
                <div style="font-size: 15px">
                    {{ $foodCategory->name }}
                    @if ($foodCategory->key != 'com')
                        : Suất {{ $foodCategory->price.',000 VND' }}
                    @endif
                </div>
            </div>
            <div class="row foood-item">
                @foreach ($foodCategory->foods as $food)
                    @if (in_array($food->id, $foodItems))
                        @if ($food->type == 1 && empty($typeOne))
                            <div class="text-center col-10 mt-3 mb-1" style="font-size: 14px; text-decoration: underline">Món chính</div>
                            @php
                                $typeOne = true;
                            @endphp
                        @elseif ($food->type == 2 && empty($typeTwo))
                            <div class="text-center col-10 mt-3 mb-1" style="font-size: 14px; text-decoration: underline">Món phụ</div>
                            @php
                                $typeTwo = true;
                            @endphp
                        @elseif ($food->type == 3 && empty($typeThree))
                            <div class="text-center col-10 mt-3 mb-1" style="font-size: 14px; text-decoration: underline">Món rau</div>
                            @php
                                $typeThree = true;
                            @endphp
                        @endif
                        <div class="form-check-item col-3">
                            <label>
                                + {{ $food->name }}
                            </label>
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