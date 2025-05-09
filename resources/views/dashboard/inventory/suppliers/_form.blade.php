@csrf
<div class="container-fluid p-0">
    <div class="card shadow-sm mb-4">

        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5>Suppliers Informatin</h5>
            <a href="{{ route('dashboard.inventory.suppliers.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Main
            </a>
        </div>
        <div class="form-group m-2">
            <label for="name">Company Name:</label>
            <input type="text" class="form-control @error('company_name') is-invalid @enderror" name="company_name"
                placeholder="Enter supplier company name" value="{{ old('company_name', $supplier->company_name) }}">
            @error('company_name')
                <small class="text-danger alert-danger">{{ $message }}</small>
            @enderror
        </div>


        <div class="form-group m-2">
            <label for="name">Contact Name:</label>
            <input type="text" class="form-control @error('contact_name') is-invalid @enderror" name="contact_name"
                placeholder="Enter supplier contact name" value="{{ old('contact_name', $supplier->contact_name) }}">
            @error('contact_name')
                <small class="text-danger alert-danger">{{ $message }}</small>
            @enderror
        </div>


        <div class="form-group m-2">
            <label for="name">Email:</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                placeholder="Enter supplier email" value="{{ old('email', $supplier->email) }}">
            @error('email')
                <small class="text-danger alert-danger">{{ $message }}</small>
            @enderror
        </div>


        <div class="form-group m-2">
            <label for="name">Phone Number:</label>
            <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                placeholder="Enter supplier phone" value="{{ old('phone', $supplier->phone) }}">
            @error('phone')
                <small class="text-danger alert-danger">{{ $message }}</small>
            @enderror
        </div>



        <div class="form-group m-2">
            <label for="name">Address:</label>
            <input type="text" class="form-control @error('address') is-invalid @enderror" name="address"
                placeholder="Enter supplier address" value="{{ old('address', $supplier->address) }}">
            @error('address')
                <small class="text-danger alert-danger">{{ $message }}</small>
            @enderror
        </div>

    </div>
</div>
