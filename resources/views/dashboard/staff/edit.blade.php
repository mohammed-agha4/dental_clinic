@extends('layouts.master.master')

@section('title', 'Staff')

@section('content')

    {{-- <h4>Edit Staff Member</h4> --}}
    <form action="{{ route('dashboard.staff.update', $staff->id) }}" method="post">
        @method('put')
        @csrf
        @include('dashboard.staff._form')
        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>
@endsection
