@props([
    'type' => 'text', 'name', 'value' => '', 'label' => 'false'
])
@if ($label)
<label for="">{{ $label }}</label>
@endif

<input
type="{{ $type }}"
name="{{ $name }}"
class="form-control @error($name) is-invalid @enderror"
value="{{ old($name, $value) }}"
{{ $attributes }}
>

@error($name)
    <small class="text-danger alert-danger">{{ $message }}</small>
@enderror
