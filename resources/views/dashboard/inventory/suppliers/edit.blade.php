
@extends('layouts.master.master')

@section('title', 'Suppliers')

@section('content')


    <form action="{{ route('dashboard.inventory.suppliers.update', $supplier->id) }}" method="post">

        @include('dashboard.inventory.suppliers._form')
        @method('put')

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>

    </form>
@endsection
