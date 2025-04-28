@extends('layouts.master.master')

@section('title', 'Tool Information')

@section('content')
    <div class="container-fluid">
        <div class="card shadow-sm mb-4">

            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Edit Tool Information</h5>
                <a href="{{ route('dashboard.inventory.inventory.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left fa-sm"></i> Back to Main
                </a>
            </div>

            <div class="card-body">
                <form action="{{ route('dashboard.inventory.inventory.update', $inventory->id) }}" method="post">
                    @method('put')
                    @csrf

                    <!-- Basic Information Section -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tool Name:</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $inventory->name) }}" required>
                                @error('name')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Category:</label>
                                <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id', $inventory->category_id) == $category->id)>
                                            {{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Supplier:</label>
                                <select name="supplier_id" class="form-control @error('supplier_id') is-invalid @enderror">
                                    <option selected disabled>--select--</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected(old('supplier_id', $inventory->supplier_id) == $supplier->id)>
                                            {{ $supplier->company_name }}</option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>SKU:</label>
                                <input type="text" name="SKU" class="form-control @error('SKU') is-invalid @enderror"
                                    value="{{ old('SKU', $inventory->SKU) }}" required>
                                @error('SKU')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="form-group">
                                <x-form.textarea label='Description:' name='description' :value='$inventory->description'
                                    placeholder="Enter description" />
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Quantity:</label>
                                <input type="number" name="quantity"
                                    class="form-control @error('quantity') is-invalid @enderror"
                                    value="{{ old('quantity', $inventory->quantity) }}" required>
                                @error('quantity')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Reorder Level:</label>
                                <input type="number" name="reorder_level"
                                    class="form-control @error('reorder_level') is-invalid @enderror"
                                    value="{{ old('reorder_level', $inventory->reorder_level) }}" required>
                                @error('reorder_level')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Unit Price:</label>
                                <input type="number" name="unit_price"
                                    class="form-control @error('unit_price') is-invalid @enderror"
                                    value="{{ old('unit_price', $inventory->unit_price) }}" required>
                                @error('unit_price')
                                    <small class="text-danger alert-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label >Expiry Date</label>
                                <input type="date" class="form-control date @error('expiry_date') is-invalid @enderror"
                                    name='expiry_date' value="{{ old('expiry_date', $inventory->expiry_date->format('Y-m-d')) }}"
                                    placeholder='Expiry Date' required>
                                @error('expiry_date')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Active:</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" @selected(old('is_active', $inventory->is_active) == '1')>Active</option>
                                    <option value="0" @selected(old('is_active', $inventory->is_active) == '0')>Not Active</option>
                                </select>
                                @error('is_active')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    {{-- <div class="text-end"> --}}
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    {{-- </div> --}}
                </form>
            </div>
        </div>
    </div>
@endsection
