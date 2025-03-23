
@extends('layouts.master.master')

@section('title', 'Services')

@section('content')

    {{-- <h4>Edit Service</h4> --}}

    <form action="{{ route('dashboard.services.update', $service->id) }}" method="post">

        @include('dashboard.services._form')
        @method('put')

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>

    </form>
@endsection
