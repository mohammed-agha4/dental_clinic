@extends('layouts.master.master')

@section('title', 'Create Supplier')

@section('content')


    <form action="{{ route('dashboard.inventory.suppliers.store') }}" method="post">
        @include('dashboard.inventory.suppliers._form')



        <button type="submit" class="btn btn-primary m-2">Insert Supplier</button>
    </form>

    </form>
@endsection

