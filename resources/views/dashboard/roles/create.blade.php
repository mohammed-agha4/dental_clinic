@extends('layouts.master.master')

@section('title', 'Create Role')

@section('content')


    <form action="{{ route('dashboard.roles.store') }}" method="post">
        @include('dashboard.roles._form')



        <button type="submit" class="btn btn-primary m-2">Insert Role</button>
    </form>

@endsection

