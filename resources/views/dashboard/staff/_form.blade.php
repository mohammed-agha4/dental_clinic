<div class="container-fluid p-0">
    <div class="card shadow-sm mb-4">
        {{-- <div class="card-header bg-light"> --}}
            {{-- <h5 class="mb-0"></h5> --}}
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h5>Staff Information</h5>
                <a href="{{ route('dashboard.staff.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left fa-sm"></i> Back to Staff
                </a>
            </div>
        {{-- </div> --}}
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">User:</label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                            <option selected disabled>--select--</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id', $staff->user_id) == $user->id)>{{ $user->name }}</option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Role:</label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror">
                            <option selected disabled>--select--</option>
                            <option @selected(old('role', $staff->role) == 'admin') value="admin">Admin</option>
                            <option @selected(old('role', $staff->role) == 'dentist') value="dentist">Dentist</option>
                            <option @selected(old('role', $staff->role) == 'assistant') value="assistant">Assistant</option>
                            <option @selected(old('role', $staff->role) == 'receptionist') value="receptionist">Receptionist</option>
                        </select>
                        @error('role')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Phone Number:</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder="Enter staff phone" value="{{ old('phone', $staff->phone) }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">License Number:</label>
                        <input type="text" class="form-control @error('license_number') is-invalid @enderror" name="license_number" placeholder="Enter staff license number" value="{{ old('license_number', $staff->license_number) }}">
                        @error('license_number')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Working Hours:</label>
                        <input type="number" class="form-control @error('working_hours') is-invalid @enderror" name="working_hours" placeholder="Enter staff Working Hours" value="{{ old('working_hours', $staff->working_hours) }}">
                        @error('working_hours')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Active:</label>
                        <select name="is_active" class="form-select @error('is_active') is-invalid @enderror">
                            <option value="1" @selected(old('is_active', $staff->is_active) == '1')>Active</option>
                            <option value="0" @selected(old('is_active', $staff->is_active) == '0')>Not Active</option>
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
