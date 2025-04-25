@csrf
<div class="container-fluid p-0">
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5>Roles Information</h5>
            <a href="{{ route('dashboard.user-roles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Main
            </a>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">User:</label>
                        <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                            <option selected disabled>--select--</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ (isset($user_role) && $user_role->user_id == $user->id) || old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Roles:</label>
                        <select name="role_id" class="form-select @error('role_id') is-invalid @enderror">
                            <option selected disabled>--select--</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}"
                                    {{ (isset($user_role) && $user_role->role_id == $role->id) || old('role_id') == $role->id ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
