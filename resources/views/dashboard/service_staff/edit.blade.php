{{-- @dd('ff') --}}
@extends('layouts.master.master')

@section('title', 'Staff Service Information')

@section('content')


    <form action="{{ route('dashboard.service-staff.update', $service_staff->id) }}" method="post">

        @include('dashboard.service_staff._form')
        @method('put')

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>

    </form>
@endsection
