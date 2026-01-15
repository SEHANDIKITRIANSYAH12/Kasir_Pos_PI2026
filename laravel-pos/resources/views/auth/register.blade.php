<x-guest-layout>
    <x-slot name="title">Sign Up</x-slot>
    <x-slot name="subtitle">Enter your details to register.</x-slot>

    <form role="form" method="POST" action="{{ route('register') }}">
        @csrf
        <div class="mb-3">
            <input type="text" name="name" class="form-control form-control-lg @error('name') is-invalid @enderror" placeholder="Name" aria-label="Name" value="{{ old('name') }}" required autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" placeholder="Email" aria-label="Email" value="{{ old('email') }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control form-control-lg @error('password') is-invalid @enderror" placeholder="Password" aria-label="Password" required>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <input type="password" name="password_confirmation" class="form-control form-control-lg" placeholder="Confirm Password" aria-label="Password Confirmation" required>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-lg btn-primary btn-lg w-100 mt-4 mb-0">Sign up</button>
        </div>
    </form>
    <div class="card-footer text-center pt-0 px-lg-2 px-1">
        <p class="mb-4 text-sm mx-auto">
            Already have an account?
            <a href="{{ route('login') }}" class="text-primary text-gradient font-weight-bold">Sign in</a>
        </p>
    </div>
</x-guest-layout>
