@if (!empty($tipToday))
   <div class="news-ticker">
        <div class="ticker-wrap">
            <div class="ticker">
            @if ($tipToday)
                Hôm nay ngày {{ date('d-m-Y', time())}} : 
                @foreach ($tipToday as $key => $item)
                    <span>🔥 Cảm ơn đồng nghiệp {{ $item['user_name'] }} đã tip cho Lunchmate {{ number_format($item['value'] * 1000) }}đ </span>
                @endforeach
            @endif
            </div>
        </div>
    </div> 
@endif