@extends('layouts.master.master')

@section('title', 'Services')

@section('content')

    {{-- <h4>Insert Service</h4> --}}

    <form action="{{ route('dashboard.services.store') }}" method="post">
        @include('dashboard.services._form')



        <button type="submit" class="btn btn-primary m-2">Insert Service</button>
    </form>

    </form>
@endsection
