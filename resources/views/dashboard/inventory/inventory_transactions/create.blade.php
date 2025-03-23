@extends('layouts.master.master')

@section('title', 'Transaction')

@section('content')


    <form action="{{ route('dashboard.inventory.inventory-transactions.store') }}" method="post">
        @include('dashboard.inventory.inventory_transactions._form')



        <button type="submit" class="btn btn-primary m-2">Insert Transaction</button>
    </form>

    </form>
@endsection


