@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Admin Dashboard') }}</div>

                <div class="card-body">
                    <h1>Selamat Datang di Dashboard Admin!</h1>
                    <p>Di sini Anda bisa mengelola produk, kategori, dan pesanan.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection