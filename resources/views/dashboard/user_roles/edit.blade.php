@extends('layouts.master.master')
@section('title', 'Edit User Role')
@section('content')
    <form action="{{ route('dashboard.user-roles.update-composite', ['user_id' => $user_role->user_id, 'role_id' => $user_role->role_id]) }}" method="POST">
        @method('PUT')
        @include('dashboard.user_roles._form')
        <div class="container-fluid p-0">
            <div class="card-footer text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update User Role
                </button>
            </div>
        </div>
    </form>
@endsection
