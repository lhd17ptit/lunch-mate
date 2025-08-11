@if (!empty($tipToday))
   <div class="news-ticker">
        <div class="ticker-wrap">
            <div class="ticker">
            @if ($tipToday)
                Hôm này ngày {{ date('d-m-Y', time())}} : 
                @foreach ($tipToday as $key => $item)
                    <span>🔥 Cảm ơn đồng nghiệp {{ $item['user_name'] }} đã tip cho lunchmate {{ number_format($item['value'] * 1000) }}đ </span>
                @endforeach
            @endif
            </div>
        </div>
    </div> 
@endif