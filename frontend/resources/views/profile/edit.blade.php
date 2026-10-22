<x-app-layout>
    <x-slot name="header">
        <h1>Mi perfil</h1>
    </x-slot>

    @include('profile.partials.update-profile-information-form')
    @include('profile.partials.update-password-form')
    @include('profile.partials.delete-user-form')
</x-app-layout>
