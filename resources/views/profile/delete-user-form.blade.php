<div>
    <!-- Delete Account Section -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Delete Account') }}</h5>
            <p class="mb-0 text-muted small">{{ __('Permanently delete your account.') }}</p>
        </div>
        <div class="card-body">
            <p class="text-muted" style="max-width: 600px;">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
            </p>
            <div class="mt-4">
                <button type="button" class="btn btn-danger" wire:click="confirmUserDeletion"
                    wire:loading.attr="disabled">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </div>
    </div>

    <!-- Delete User Confirmation Modal -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1" role="dialog"
        aria-labelledby="deleteAccountModalLabel" aria-hidden="true" wire:model.live="confirmingUserDeletion">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAccountModalLabel">{{ __('Delete Account') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        wire:click="$toggle('confirmingUserDeletion')"></button>
                </div>
                <div class="modal-body">
                    <p>
                        {{ __('Are you sure you want to delete your account? Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>
                    <div class="mt-3" x-data="{}"
                        x-on:confirming-delete-user.window="setTimeout(() => $refs.password.focus(), 250)">
                        <input type="password" class="form-control" placeholder="{{ __('Password') }}"
                            autocomplete="current-password" x-ref="password" wire:model="password"
                            wire:keydown.enter="deleteUser">
                        <x-input-error for="password" class="mt-2" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        wire:click="$toggle('confirmingUserDeletion')" wire:loading.attr="disabled">
                        {{ __('Cancel') }}
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="deleteUser" wire:loading.attr="disabled">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
