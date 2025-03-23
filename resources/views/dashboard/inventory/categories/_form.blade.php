@csrf
<div class="container-fluid p-0">
    <div class="card shadow-sm mb-4">

        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5>Categories Informatin</h5>
            <a href="{{ route('dashboard.inventory.categories.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Main
            </a>
        </div>
    <div class="form-group m-2">
        <label for="name">Name:</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" placeholder="Enter category name"
            value="{{ old('name', $category->name) }}">
        @error('name')
            <small class="text-danger alert-danger">{{ $message }}</small>
        @enderror
    </div>

    </div>
</div>






