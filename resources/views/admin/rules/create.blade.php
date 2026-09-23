@extends('layouts.admin')

@section('title', 'New rule')

@section('content')
    <h1>New rule — {{ $endpoint->name }}</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.endpoints.rules.store', $endpoint) }}">
            @csrf
            @include('admin.rules._form', ['rule' => null])
            <button class="btn btn-primary" type="submit">Create rule</button>
        </form>
    </div>
@endsection
