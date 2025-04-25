@extends('layouts.master.master')
@section('title', 'Create User Role')
@section('content')
    <form action="{{ route('dashboard.user-roles.store') }}" method="POST">
        @include('dashboard.user_roles._form')
        <div class="container-fluid p-0">
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create User Role
                </button>
            </div>
        </div>
    </form>
@endsection
