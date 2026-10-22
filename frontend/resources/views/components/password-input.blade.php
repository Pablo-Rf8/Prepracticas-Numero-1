@props(['disabled' => false])

<div class="password-field" x-data="{ passwordVisible: false }">
    <input type="password" x-bind:type="passwordVisible ? 'text' : 'password'" @disabled($disabled) {{ $attributes->merge(['class' => 'field-input']) }}>

    <button
        type="button"
        class="password-toggle"
        x-cloak
        x-on:click="passwordVisible = !passwordVisible"
        x-bind:aria-pressed="passwordVisible"
        x-bind:aria-label="passwordVisible ? 'Ocultar contraseña' : 'Mostrar contraseña'"
        aria-controls="{{ $attributes->get('id') }}"
        @disabled($disabled)
        x-text="passwordVisible ? 'Ocultar' : 'Mostrar'"
    >Mostrar</button>
</div>
