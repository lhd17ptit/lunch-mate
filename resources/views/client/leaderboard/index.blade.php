<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Người ủng hộ | LunchMate</title>
    <meta name="description" content="Bảng xếp hạng những khách hàng tip nhiều nhất trên LunchMate. Xem ai là tipper hào phóng nhất và tham gia đua top để nhận quà đặc biệt.">
    <meta name="keywords" content="bảng xếp hạng tip, top tipper, tipper hào phóng, leaderboard tip, đua top tip, LunchMate, thưởng tip, top khách hàng, tip nhiều nhất">
    <meta name="author" content="Lunch Mate">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="Người ủng hộ hào phóng | LunchMate">
    <meta property="og:description" content="Khám phá top khách hàng tip nhiều nhất, cạnh tranh vị trí tipper hào phóng và nhận phần thưởng hấp dẫn.">
    <meta property="og:image" content="{{ asset('admin/assets/images/logos/leaderboard-tip-banner.png') }}">
    <meta property="og:url" content="https://lunchmate.online/leaderboard-tip">
    <meta property="og:type" content="website">

    <link rel="icon" type="image/png" href="{{ asset('admin/assets/favicon_io/favicon-32x32.png') }}" sizes="32x32">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('client/assets/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('client/assets/leaderboard.css') }}">
</head>
<body>
    <div class="row p-0 m-0">
        <div class="col-md-10 col-12 w-100">
            <h1 class="text-center mt-4 mb-4" style="font-weight:600">Danh sách người ủng hộ</h1>
            <main class="mt-5">
                <div id="leaderboard">
                    <div class="ribbon"></div>
                    <table>
                        @foreach($listUserTip as $k => $item)
                            @if ($item['top'] <= 5)
                                <tr>
                                    <td class="number">
                                        @if ($item['top'] == 1)
                                            <img class="icon-top-1" src="{{ asset('client/assets/images/dragon-white.png')}}" alt="dragon"/>
                                        @else
                                            {{ $item['top'] }}
                                        @endif
                                    </td>
                                    <td class="name name-top-1">{{ $item['user_name'] }}</td>
                                    <td class="points">
                                        {{ number_format($item['value'] * 1000).'đ' }} 
                                        @if ($item['top'] == 1)
                                            <img class="gold-medal" src="{{ asset('client/assets/images/top1.png')}}" alt="gold medal"/>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
                </main>
                <div class="d-flex justify-content-center">
                    <a class="mt-4 text-center" href="{{ route('landingPage') }}" title="Home" style="text-decoration: underline">Về trang chủ</a>
                </div>

        </div>
    </div>

    <div id="news_tip"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="{{ asset('admin/assets/js/toastr.js') }}"></script>

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
    
</body>
</html>