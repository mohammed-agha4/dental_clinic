@extends('layouts.master.master')

@section('title', 'Link Staff To Service')

@section('content')


    <form action="{{ route('dashboard.service-staff.store') }}" method="post">
        @include('dashboard.service_staff._form')



        <button type="submit" class="btn btn-primary m-2">Insert staff services</button>
    </form>

    </form>
@endsection


