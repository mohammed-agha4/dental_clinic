@extends('layouts.master.master')

@section('title', 'Staff')

@section('content')

    {{-- <h4>Insert Doctor</h4> --}}
    <form action="{{ route('dashboard.staff.store') }}" method="post">
        @csrf

@include('dashboard.staff._form')


        <button type="submit" class="btn btn-primary m-2">Insert Doctor</button>
    </form>

@endsection
