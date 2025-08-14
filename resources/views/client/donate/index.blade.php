<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Donate Form</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('client/assets/custom.css') }}">

</head>
<body class="bg-light">

	<div class="m-3">
		<a href="{{ route('landingPage') }}" class="btn btn-outline-danger">
			&larr; Back
		</a>
	</div>

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-4">Donate cho LunchMate</h4>
            
            <form id="donate-form" action="{{ route('donate') }}" method="POST">
				@csrf
				<select name="user_id" id="user_id" class="form-control mt-4">
					<option value="">Chọn người dùng</option>
					@foreach ($users as $user)
						<option value="{{ $user->id }}">{{ $user->name }}</option>
					@endforeach
				</select>

				<div class="mt-3">
					<label for="amount" class="form-label">Số tiền</label>
					<input type="text" class="form-control" name="amount_display" id="amount_display" placeholder="Enter a number">
                    <input type="hidden" name="amount" id="amount">
				</div>

				<!-- Message Input -->
				<div class="mt-3">
					<label for="message" class="form-label">Tin nhắn</label>
					<textarea class="form-control" name="message" id="message" rows="4" maxlength="255" placeholder="Enter your message"></textarea>
				</div>

				<!-- Submit Button -->
				<button type="submit" class="btn btn-primary">Submit</button>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

	<script>
		$(document).ready(function() {
            $('#user_id').select2();
		});

        $('#amount_display').on('input', function() {
            let raw = $(this).val().replace(/[^\d]/g, '');
    
            // Add thousand separators for display
            let formatted = raw.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            $(this).val(formatted);
            
            // Update hidden input with numeric value
            $('#amount').val(raw);
        });

        $('#donate-form').on('submit', function(e){
            if(parseInt($('#amount').val(), 10) < 2000){
                e.preventDefault();
                alert('Donate amount must be at least 2,000');
            }
        });
	</script>
</body>
</html>
