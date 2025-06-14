@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Result</div>

                <div class="card-body">
                    @if($status == '00')
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Payment Successful!</h4>
                            <p>Transaction ID: {{ $transaction_id }}</p>
                            <p>Message: {{ $message }}</p>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Payment Failed</h4>
                            <p>Status Code: {{ $status }}</p>
                            <p>Message: {{ $message }}</p>
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('vnpay.sandbox') }}" class="btn btn-primary">Back to Payment Form</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 