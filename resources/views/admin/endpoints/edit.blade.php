@extends('layouts.admin')

@section('title', 'Edit endpoint')

@section('content')
    <h1>Edit {{ $endpoint->name }}</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.endpoints.update', $endpoint) }}">
            @csrf
            @method('PUT')
            @include('admin.endpoints._form')
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    </div>
@endsection
