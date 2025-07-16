@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">PayOs Sandbox Payment</div>

                {{-- QR implementation --}}
                {{-- <img id="payment-image" class="d-none" style="max-width: 100%;" width="400" height="400" /> --}}

                <div class="card-body">
                    <form id="payOsForm" method="POST" action="{{ route('payos.process') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="amount">Amount (VND)</label>
                            <input type="number" class="form-control" min="10000" id="amount" name="amount" required>
                            <small class="form-text text-muted">Minimum amount: 10,000 VND</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Order Info</label>
                            <input type="text" class="form-control" id="description" name="description" required>
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
    const form = $('#payOsForm');
    const submitBtn = $('#submitBtn');
    const spinner = submitBtn.find('.spinner-border');

    // QR implementation
    // form.on('submit', function(e) {
    //     e.preventDefault();
        
    //     // Disable submit button and show spinner
    //     submitBtn.prop('disabled', true);
    //     spinner.removeClass('d-none');
        
    //     $.ajax({
    //         url: form.attr('action'),
    //         method: 'POST',
    //         data: form.serialize(),
    //         xhrFields: {
    //             responseType: 'blob' // <== important
    //         },
    //         success: function(blob) {
    //             const url = URL.createObjectURL(blob);
    //             $('#payment-image')
    //                 .attr('src', url)
    //                 .removeClass('d-none');

    //             submitBtn.prop('disabled', false);
    //             spinner.addClass('d-none');
    //         },
    //         error: function(xhr) {
    //             console.error('Error:', xhr);
    //             alert('Error processing payment. Please try again.');
    //             submitBtn.prop('disabled', false);
    //             spinner.addClass('d-none');
    //         }
    //     });
    // });

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