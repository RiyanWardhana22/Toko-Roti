@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Tambah Kategori Baru</h2>

    <form action="{{ route('categories.store') }}" method="POST">
        @csrf
        <div class="form-group mb-3">
            <label for="name">Nama Kategori</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('categories.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection