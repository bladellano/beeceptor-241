@extends('layouts.admin')

@section('title', 'New endpoint')

@section('content')
    <h1>New endpoint</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.endpoints.store') }}">
            @csrf
            @include('admin.endpoints._form', ['endpoint' => null])
            <button class="btn btn-primary" type="submit">Create</button>
        </form>
    </div>
@endsection
