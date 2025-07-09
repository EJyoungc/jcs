<form wire:submit.prevent="updatePassword">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Update Password') }}</h5>
            <small class="text-muted">{{ __('Ensure your account is using a long, random password to stay secure.') }}</small>
        </div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label for="current_password">{{ __('Current Password') }}</label>
                <input id="current_password" type="password" class="form-control" wire:model="state.current_password" autocomplete="current-password">
                <x-input-error for="current_password" class="mt-2" />
            </div>

            <div class="form-group mb-3">
                <label for="password">{{ __('New Password') }}</label>
                <input id="password" type="password" class="form-control" wire:model="state.password" autocomplete="new-password">
                <x-input-error for="password" class="mt-2" />
            </div>

            <div class="form-group mb-3">
                <label for="password_confirmation">{{ __('Confirm Password') }}</label>
                <input id="password_confirmation" type="password" class="form-control" wire:model="state.password_confirmation" autocomplete="new-password">
                <x-input-error for="password_confirmation" class="mt-2" />
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end align-items-center">
            <div class="me-3">
                <x-action-message on="saved">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
            <button type="submit" class="btn btn-primary">
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>
