@csrf

<div class="container-fluid">
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h4>Transactions</h4>
            <a href="{{ route('dashboard.inventory.inventory-transactions.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Main
            </a>
        </div>

        <div class="card-body">
            <div class="form-group w-50 m-2">
                <label>Inventory Tool:</label>
                <select name="inventory_id" class="form-control @error('inventory_id') is-invalid @enderror">
                    <option selected disabled>--select--</option>
                    @foreach ($inventory as $item)
                        <option value="{{ $item->id }}" @selected(old('inventory_id', $inventory_transaction->inventory_id ?? '') == $item->id)>
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
                @error('inventory_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group w-50 m-2">
                <label>Staff:</label>
                <select name="staff_id" class="form-control @error('staff_id') is-invalid @enderror">
                    <option selected disabled>--select--</option>
                    @foreach ($staff as $staffMember)
                        <option value="{{ $staffMember->id }}" @selected(old('staff_id', $inventory_transaction->staff_id ?? '') == $staffMember->id)>
                            {{ $staffMember->user->name }}
                        </option>
                    @endforeach
                </select>
                @error('staff_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group w-50 m-2">
                <label>Quantity:</label>
                <input type="number" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                       value="{{ old('quantity', $inventory_transaction->quantity ?? '') }}" placeholder="Quantity">
                @error('quantity')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group w-50 m-2">
                <label>Unit Price:</label>
                <input type="number" name="unit_price" class="form-control @error('unit_price') is-invalid @enderror"
                       value="{{ old('unit_price', $inventory_transaction->unit_price ?? '') }}" placeholder="Unit Price" step="0.01">
                @error('unit_price')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group w-50 m-2">
                <label>Transaction Date:</label>
                <input type="date" name="transaction_date" class="form-control @error('transaction_date') is-invalid @enderror"
                       value="{{ old('transaction_date', isset($inventory_transaction->transaction_date) ? $inventory_transaction->transaction_date->format('Y-m-d') : '' ) }}">
                @error('transaction_date')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group w-50 m-2">
                <label>Notes:</label>
                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                          placeholder="Enter notes">{{ old('notes', $inventory_transaction->notes ?? '') }}</textarea>
                @error('notes')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>
