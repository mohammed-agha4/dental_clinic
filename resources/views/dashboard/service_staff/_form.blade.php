@csrf
<div class="container-fluid p-0">
    <div class="card shadow-sm mb-4">
        {{-- <div class="card-header bg-light">
            <h5 class="mb-0">Link Staff to Service</h5>

        </div> --}}
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5>Link Staff to Service</h5>
            <a href="{{ route('dashboard.service-staff.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Main
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Staff:</label>
                        <select name="staff_id" class="form-select @error('staff_id') is-invalid @enderror">
                            <option selected disabled>--select--</option>
                            @foreach ($staff as $staff)
                                <option value="{{ $staff->id }}" @selected(old('staff_id', $service_staff->staff_id) == $staff->id)>
                                    {{ $staff->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('staff_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Services:</label>
                        <select name="service_id" class="form-select @error('service_id') is-invalid @enderror">
                            <option selected disabled>--select--</option>
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}" @selected(old('service_id', $service_staff->service_id) == $service->id)>
                                    {{ $service->service_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
