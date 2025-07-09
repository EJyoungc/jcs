<div>
    <x-slot name="header">
        <h2 class="h2 text-dark">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="container py-5">
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            @livewire('profile.update-profile-information-form')
            <hr>
        @endif

        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <div class="mt-4">
                @livewire('profile.update-password-form')
            </div>
            <hr>
        @endif

        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <div class="mt-4">
                @livewire('profile.two-factor-authentication-form')
            </div>
            <hr>
        @endif

        <div class="mt-4">
            @livewire('profile.logout-other-browser-sessions-form')
        </div>

        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <hr>
            <div class="mt-4">
                @livewire('profile.delete-user-form')
            </div>
        @endif
    </div>
</div>
