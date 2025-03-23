@csrf



<div class="container-fluid">
    <div class="card shadow-sm mb-4">

        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5>Service Information</h5>
            <a href="{{ route('dashboard.services.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Main
            </a>
        </div>


        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="form-group">

                        <label>Service Name:</label>
                        <input type="text" name="service_name"
                            class="form-control @error('service_name') is-invalid @enderror"
                            placeholder='Enter Service Name' value="{{ old('service_name', $service->service_name) }}"
                            required>
                        @error('service_name')
                            <small class="text-danger alert-danger">{{ $message }}</small>
                        @enderror

                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-8">
                    <div class="form-group">
                        <x-form.textarea label='Description:' name='description' :value='$service->description'
                            placeholder="Enter service description" />
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-group">

                        <label>Service Price:</label>
                        <input type="number" name="service_price"
                            class="form-control @error('service_price') is-invalid @enderror"
                            placeholder='Enter Service Price'
                            value="{{ old('service_price', $service->service_price) }}" required>
                        @error('service_price')
                            <small class="text-danger alert-danger">{{ $message }}</small>
                        @enderror

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">

                        <label>Duration (minutes):</label>
                        <input type="number" name="duration"
                            class="form-control @error('duration') is-invalid @enderror"
                            placeholder='Enter Service duration' value="{{ old('duration', $service->duration) }}"
                            required>
                        @error('duration')
                            <small class="text-danger alert-danger">{{ $message }}</small>
                        @enderror

                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group status-section">
                        <label class="form-label">Service Status:</label>
                        <select name="is_active" class="form-select" required>
                            <option value="1" @selected(old('is_active', $service->is_active) == '1')>Active</option>
                            <option value="0" @selected(old('is_active', $service->is_active) == '0')>Not Active</option>
                        </select>
                        @error('is_active')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
