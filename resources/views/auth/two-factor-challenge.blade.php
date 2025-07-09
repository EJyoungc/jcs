<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two Factor Challenge</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { margin-top: 50px; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <!-- Placeholder for logo -->
                        <img src="/path/to/your/logo.png" alt="Logo" class="img-fluid" style="max-width: 150px;">
                    </div>
                    <div class="card-body">
                        <div x-data="{ recovery: false }">
                            <div class="mb-4 text-muted" x-show="! recovery">
                                {{ __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}
                            </div>

                            <div class="mb-4 text-muted" x-cloak x-show="recovery">
                                {{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}
                            </div>

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('two-factor.login') }}">
                                @csrf

                                <div class="mb-3" x-show="! recovery">
                                    <label for="code" class="form-label">{{ __('Code') }}</label>
                                    <input id="code" type="text" inputmode="numeric" name="code" class="form-control" autofocus x-ref="code" autocomplete="one-time-code">
                                </div>

                                <div class="mb-3" x-cloak x-show="recovery">
                                    <label for="recovery_code" class="form-label">{{ __('Recovery Code') }}</label>
                                    <input id="recovery_code" type="text" name="recovery_code" class="form-control" x-ref="recovery_code" autocomplete="one-time-code">
                                </div>

                                <div class="d-flex justify-content-end align-items-center mt-4">
                                    <button type="button" class="btn btn-link text-muted text-decoration-none"
                                                    x-show="! recovery"
                                                    x-on:click="
                                                        recovery = true;
                                                        $nextTick(() => { $refs.recovery_code.focus() })
                                                    ">
                                        {{ __('Use a recovery code') }}
                                    </button>

                                    <button type="button" class="btn btn-link text-muted text-decoration-none"
                                                    x-cloak
                                                    x-show="recovery"
                                                    x-on:click="
                                                        recovery = false;
                                                        $nextTick(() => { $refs.code.focus() })
                                                    ">
                                        {{ __('Use an authentication code') }}
                                    </button>

                                    <button type="submit" class="btn btn-primary ms-4">
                                        {{ __('Log in') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>