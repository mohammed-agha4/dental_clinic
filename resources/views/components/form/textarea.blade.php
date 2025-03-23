@props([
    'name', 'value' => '', 'label' => 'false'
])
@if ($label)
<label for="">{{ $label }}</label>
@endif

<textarea
name="{{ $name }}"
class="form-control @error('{{ $name }}') is-invalid @enderror"
{{ $attributes }}
cols="{{ $cols ?? '30' }}"
rows="{{ $rows ?? '3' }}"
>{{ old($name, $value) }}</textarea>
@error('{{ $name }}')
    <small class="text-danger alert-danger">{{ $message }}</small>
@enderror
