@extends('layouts.admin')

@section('title', 'Edit rule')

@section('content')
    <h1>Edit rule — {{ $endpoint->name }}</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.endpoints.rules.update', [$endpoint, $rule]) }}">
            @csrf
            @method('PUT')
            @include('admin.rules._form')
            <button class="btn btn-primary" type="submit">Save rule</button>
        </form>
    </div>
@endsection
