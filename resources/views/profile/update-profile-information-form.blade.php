<form wire:submit.prevent="updateProfileInformation">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Profile Information') }}</h5>
            <small class="text-muted">{{ __('Update your account\'s profile information and email address.') }}</small>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Profile Photo -->
                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                    <div x-data="{ photoName: null, photoPreview: null }" class="col-md-6 mb-3">
                        <div class="form-group">
                            <!-- Profile Photo File Input -->
                            <input type="file" id="photo" class="d-none"
                                   wire:model.live="photo"
                                   x-ref="photo"
                                   x-on:change="
                                        photoName = $refs.photo.files[0].name;
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            photoPreview = e.target.result;
                                        };
                                        reader.readAsDataURL($refs.photo.files[0]);
                                   ">

                            <label for="photo" class="form-label">{{ __('Photo') }}</label>

                            <!-- Current Profile Photo -->
                            <div class="mt-2" x-show="! photoPreview">
                                <img src="{{empty(auth()->user()->profile_photo_path) ? auth()->user()->avatarUrl() : asset('/uploads/'.auth()->user()->profile_photo_path)  }}" alt="{{ $this->user->name }}" class="rounded-circle" style="width:80px; height:80px; object-fit:cover;">
                                {{-- <img src=" {{ Auth::user()->avatarUrl() }}" alt="" class="rounded-circle" style="width:80px; height:80px; object-fit:cover;" srcset=""> --}}

                            </div>

                            <!-- New Profile Photo Preview -->
                            <div class="mt-2" x-show="photoPreview" style="display: none;">
                                <span class="d-block rounded-circle" style="width:80px; height:80px; background-size:cover; background-position:center;"
                                      x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                </span>
                            </div>

                            <button type="button" class="btn btn-secondary mt-2 me-2" x-on:click.prevent="$refs.photo.click()">
                                {{ __('Select A New Photo') }}
                            </button>

                            @if ($this->user->profile_photo_path)
                                <button type="button" class="btn btn-danger mt-2" wire:click="deleteProfilePhoto">
                                    {{ __('Remove Photo') }}
                                </button>
                            @endif

                            <x-input-error for="photo" class="mt-2" />
                        </div>
                    </div>
                @endif

                <!-- Name -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="name" class="form-label">{{ __('Name') }}</label>
                        <input id="name" type="text" class="form-control" wire:model="state.name" required autocomplete="name">
                        <x-input-error for="name" class="mt-2" />
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <label for="email" class="form-label">{{ __('Email') }}</label>
                        <input id="email" type="email" class="form-control" wire:model="state.email" required autocomplete="username">
                        <x-input-error for="email" class="mt-2" />

                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $this->user->hasVerifiedEmail())
                            <p class="small mt-2">
                                {{ __('Your email address is unverified.') }}
                                <button type="button" class="btn btn-link p-0" wire:click.prevent="sendEmailVerification">
                                    {{ __('Click here to re-send the verification email.') }}
                                </button>
                            </p>

                            @if ($this->verificationLinkSent)
                                <p class="mt-2 small text-success">
                                    {{ __('A new verification link has been sent to your email address.') }}
                                </p>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end align-items-center">
            <div class="me-3">
                <x-action-message on="saved">
                    {{ __('Saved.') }}
                </x-action-message>
            </div>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="photo">
                {{ __('Save') }}
            </button>
        </div>
    </div>
</form>
