@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Payment Result</div>

                <div class="card-body">
                    @if($code == '00')
                        <div class="alert alert-success">
                            <h4 class="alert-heading">Payment Successful!</h4>
                            <p>Transaction ID: {{ $orderCode }}</p>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            <h4 class="alert-heading">Payment Failed</h4>
                            <p>Status Code: {{ $code }}</p>
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('menuByShop', ['shop' => 'lunch-mate']) }}" class="btn btn-primary">Back to Menu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 