@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'notice', 'role' => 'status']) }}>
        {{ $status }}
    </div>
@endif
