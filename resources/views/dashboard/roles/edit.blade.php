
@extends('layouts.master.master')

@section('title', 'Role Information')

@section('content')


    <form action="{{ route('dashboard.roles.update', $role->id) }}" method="post">

        @include('dashboard.roles._form')
        @method('put')

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>

    </form>
@endsection
