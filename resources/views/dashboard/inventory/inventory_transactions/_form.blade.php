@csrf

<div class="container-fluid ">
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
                        <option value="{{ $item->id }}" @selected(old('inventory_id', $inventory_transaction->inventory_id) == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </select>
                @error('inventory_id')
                    <small class="text-danger alert-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group w-50 m-2">
                <label>Staff:</label>
                <select name="staff_id" class="form-control @error('staff_id') is-invalid @enderror">
                    <option selected disabled>--select--</option>
                    @foreach ($staff as $staffMember)
                        <option value="{{ $staffMember->id }}" @selected(old('staff_id', $inventory_transaction->staff_id) == $staffMember->id)>
                            {{ $staffMember->user->name }}
                        </option>
                    @endforeach
                </select>
                @error('staff_id')
                    <small class="text-danger alert-danger">{{ $message }}</small>
                @enderror
            </div>



            <div class="form-group w-50 m-2">
                <x-form.input label='Quantity' type='number' name='quantity' :value='$inventory_transaction->quantity'
                    placeholder='Quantity' />
                @error('quantity')
                    <small class="text-danger alert-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group w-50 m-2">
                <x-form.input label='Unit Price' type='number' name='unit_price' :value='$inventory_transaction->unit_price'
                    placeholder='Unit Price' />
                @error('unit_price')
                    <small class="text-danger alert-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group w-50 m-2">
                <x-form.input label='Transaction Date' type='date' name='transaction_date' :value='$inventory_transaction->transaction_date'
                    placeholder='Transaction Date' />
            </div>

            <div class="form-group w-50 m-2">
                <x-form.textarea label='Notes:' name='notes' :value='$inventory_transaction->notes' placeholder="Enter notes" />
                @error('notes')
                    <small class="text-danger alert-danger">{{ $message }}</small>
                @enderror
            </div>
        </div>
    </div>
</div>
