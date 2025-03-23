
@extends('layouts.master.master')

@section('title', 'Categories')

@section('content')


    <form action="{{ route('dashboard.inventory.categories.update', $category->id) }}" method="post">

        @include('dashboard.inventory.categories._form')
        @method('put')

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>

    </form>
@endsection
