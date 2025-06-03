<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mời tham gia - LunchMate</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        .header {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color: #333;
        }

        .content {
            margin: 20px 0;
            font-size: 16px;
            color: #555;
        }

        .cta-button {
            display: block;
            text-align: center;
            background: #007bff;
            color: white;
            padding: 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            width: 50%;
            margin: auto;
        }

        .cta-button:hover {
            background: #0056b3;
        }

        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 20px;
        }

        .footer a {
            color: #007bff;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">Lunch Mate</div>
        {{-- <div style="text-align: center">
            <img src="{{asset('images/logo.jpg')}}" alt="">
        </div> --}}
        <div class="content">
            <p><strong>Xin chào </strong>{{$admin->name}},</p>
            <p>Tôi là admin của hệ thống LunchMate. Tôi muốn mời bạn tham gia vào hệ thống của chúng tôi với vai trò là admin của hệ thống</p>
            <p>Chúng tôi rất vui khi có sự tham dự của bạn.</p>
            <p>Đây là thông tin đăng nhập:</p>
            <p>Email: {{ $admin->email}} <br> Mật khẩu: {{ $password }}</p>
            <br>
            <a href="{{$url}}" class="cta-button">Đi tới hệ thống</a>
            <br>
        </div>
        <div class="footer">
            <p>Nếu bạn gặp sự cố khi nhấp vào nút, hãy sao chép và dán liên kết này vào trình duyệt của bạn:</p>
            <p><a href="{{$url}}">{{$url}}</a></p>
            <p>Trân trọng, <br>Dịch vụ khách hàng, LunchMate</p>
        </div>
    </div>
</body>

</html>