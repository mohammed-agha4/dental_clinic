{{-- @props([
    'users', 'name' => 'user_id', 'selectedUserId' => null, 'label' => false
])

@if ($label)
<label for="">{{ $label }}</label>
@endif

<select name="{{ $name }}" class="form-control @error($name) is-invalid @enderror">
    <option selected disabled>--select--</option>
    @foreach ($users as $user)
        <option value="{{ $user->id }}" @selected(old($name, $selectedUserId) == $user->id)>{{ $user->name }}</option>
    @endforeach
</select>

@error($name)
    <small class="text-danger alert-danger">{{ $message }}</small>
@enderror --}}
