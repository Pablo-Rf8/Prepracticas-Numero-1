@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'field-error', 'aria-live' => 'polite']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
