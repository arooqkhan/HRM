<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-0" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form id="login-form" method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>

    <script>
        $(document).ready(function() {
            // Adding a focus effect to the input fields
            $('input').focus(function() {
                $(this).css('background-color', '#f0f8ff');
            }).blur(function() {
                $(this).css('background-color', '#fff');
            });

            // Animating the form on submit
            $('#login-form').on('submit', function(e) {
                e.preventDefault();
                $(this).fadeOut(500, function() {
                    $(this).submit();
                });
            });

            // Adding a hover effect to the button
            $('.ms-4').hover(
                function() {
                    $(this).css('background-color', '#4CAF50');
                }, function() {
                    $(this).css('background-color', '');
                }
            );
        });
    </script>
</x-guest-layout>
