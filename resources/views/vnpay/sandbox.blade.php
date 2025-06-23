@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">VNPay Sandbox Payment</div>

                <div class="card-body">
                    <form id="vnpayForm" method="POST" action="{{ route('vnpay.process') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="vnp_Amount">Amount (VND)</label>
                            <input type="number" class="form-control" min="10000" id="vnp_Amount" name="vnp_Amount" required>
                            <small class="form-text text-muted">Minimum amount: 10,000 VND</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="vnp_OrderInfo">Order Info</label>
                            <input type="text" class="form-control" id="vnp_OrderInfo" name="vnp_OrderInfo" required>
                            <small class="form-text text-muted">Enter order description or reference</small>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Process Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    const form = $('#vnpayForm');
    const submitBtn = $('#submitBtn');
    const spinner = submitBtn.find('.spinner-border');

    form.on('submit', function(e) {
        e.preventDefault();
        
        // Disable submit button and show spinner
        submitBtn.prop('disabled', true);
        spinner.removeClass('d-none');
        
        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (response.redirect_url) {
					console.log(response)
                    window.location.href = response.redirect_url;
                } else {
                    alert('Error processing payment. Please try again.');
                    submitBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Error processing payment. Please try again.');
                submitBtn.prop('disabled', false);
                spinner.addClass('d-none');
            }
        });
    });
});
</script>
@endpush
@endsection 