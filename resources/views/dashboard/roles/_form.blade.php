@csrf
<div class="container-fluid p-0">
    <div class="card shadow-sm mb-4">

        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h5>Roles Information</h5>
            <a href="{{ route('dashboard.roles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left fa-sm"></i> Back to Main
            </a>
        </div>
        <div class="form-group m-2">
            <label for="name">Role Name:</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                placeholder="Enter Role ame" value="{{ old('name', $role->name) }}">
            @error('name')
                <small class="text-danger alert-danger">{{ $message }}</small>
            @enderror
        </div>

        <fieldset class="mx-3">
            <legend>{{ 'Abilities' }}</legend>

            @foreach (config('abilities') as $ability_code => $ability_name)
                <div class="row mb-2">
                    {{-- <hr class="mx-2"> --}}
                    <div class="col-md-6">
                        {{ $ability_name }}
                    </div>

                    <div class="col-md-3">
                        <label><input type="radio" name="abilities[{{ $ability_code }}]" value="allow" checked
                            @checked(($role_abilities[$ability_code] ?? '') == 'allow')> Allow</label>
                    </div>

                    <div class="col-md-3">
                        <label><input type="radio" name="abilities[{{ $ability_code }}]" value="deny"
                            @checked(($role_abilities[$ability_code] ?? '') == 'deny')> Deny</label>
                    </div>

                    {{-- <div class="col-md-2">
                        <label><input type="radio" name="abilities[{{ $ability_code }}]" value="inherit"
                            @checked(($role_abilities[$ability_code] ?? '') == 'inherit')> Inherit</label>
                    </div> --}}
                    <hr class="my-2">
                </div>
            @endforeach
        </fieldset>

    </div>
</div>
