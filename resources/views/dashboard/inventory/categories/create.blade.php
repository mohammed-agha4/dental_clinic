@extends('layouts.master.master')

@section('title', 'Create Category')

@section('content')


    <form action="{{ route('dashboard.inventory.categories.store') }}" method="post">
        @include('dashboard.inventory.categories._form')



        <button type="submit" class="btn btn-primary m-2">Insert Category</button>
    </form>

    </form>
@endsection

