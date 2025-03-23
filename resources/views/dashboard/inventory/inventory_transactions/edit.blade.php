
@extends('layouts.master.master')

@section('title', 'Transaction')

@section('content')


    <form action="{{ route('dashboard.inventory.inventory-transactions.update', $inventory_transaction->id) }}" method="post">

        @include('dashboard.inventory.inventory_transactions._form')
        @method('put')

        <button type="submit" class="btn btn-primary m-2">Save Changes</button>
    </form>

    </form>
@endsection
