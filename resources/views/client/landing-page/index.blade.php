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
    <link rel="stylesheet" href="{{ asset('client/assets/custom.css') }}">
</head>
<body>
    <div class="main-container">
        <div class="menu">
            <a href="{{ route('list-order')}}" title="List order" target="_blank">Lịch sử đơn hàng</a>
            <a href="{{ route('leaderboard')}}" title="Leaderboard" target="_blank">Bảng xếp hạng</a>
            <a href="{{ route('donate-page')}}" title="Donate" target="_blank">Donate</a>
        </div>
        <div class="hero">
        <h1>Ăn ngon cả ngày - Đặt nhanh vài giây</h1>
        <p>
            Từ bữa sáng đầy năng lượng, cơm trưa văn phòng tiện lợi, đến đồ ăn nhẹ buổi chiều -
            mọi thứ đều sẵn sàng để bạn đặt ngay tại đây
        </p>
        </div>

        <div class="cards">
        <div class="card" style="background: #FFFDF4">
            @if (empty($breakfastMate))
                <div class="coming-soon">
                    <img src="{{ asset('client/assets/images/coming-soon.png')}}" alt="icon">
                </div>
            @endif
            
            <div class="d-flex justify-content-center">
                <img src="{{ asset('client/assets/images/logo-breakfast.png')}}" alt="Breakfast Icon" class="icon">
            </div>
            <p><strong>BreakfastMate</strong> - Bữa sáng tiện lợi, nhanh và ngon miệng. Giúp bạn tiết kiệm thời gian và tràn đầy năng lượng để bắt đầu ngày mới hiệu quả.</p>
            @if (!empty($breakfastMate))
                <a href="{{ route('menuByShop', ['shop' => $breakfastMate])}}" title="Menu breakfast mate"><button>Đặt ngay →</button></a>
            @endif
        </div>

        <div class="card" style="background: #FAF7E6">
            @if (empty($lunchMate))
                <div class="coming-soon">
                    <img src="{{ asset('client/assets/images/coming-soon.png')}}" alt="icon">
                </div>
            @endif
            <div class="d-flex justify-content-center">
                <img src="{{ asset('client/assets/images/logo-lunch.png')}}" alt="Lunch Icon" class="icon">
            </div>
            <p><strong>LunchMate</strong> mang đến bữa trưa ngon chuẩn vị Việt, sạch, đúng giờ - giúp bạn no bụng và tràn đầy năng lượng cho buổi chiều làm việc.</p>
            @if (!empty($lunchMate))
                <a href="{{ route('menuByShop', ['shop' => $lunchMate])}}" title="Menu lunch mate"><button>Đặt ngay →</button></a>
            @endif
        </div>

        <div class="card" style="background: #F0ECD2">
            @if (empty($afternoonMate))
                <div class="coming-soon">
                    <img src="{{ asset('client/assets/images/coming-soon.png')}}" alt="icon">
                </div>
            @endif
            <div class="d-flex justify-content-center">
                <img src="{{ asset('client/assets/images/logo-afternoon.png')}}" alt="After Icon" class="icon">
            </div>
            <p><strong>AfternoonMate</strong> - Đồ ăn chiều tiện lợi, đa dạng và ngon miệng. Giúp bạn nạp lại năng lượng và tận hưởng khoảng thời gian thư giãn cuối ngày.</p>
            @if (!empty($afternoonMate))
                <a href="{{ route('menuByShop', ['shop' => $afternoonMate])}}" title="Menu afternoon mate"><button>Đặt ngay →</button></a>
            @endif
        </div>
        </div>
    </div>
<div id="news_tip"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function() {
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
           
        });
    </script>
</html>