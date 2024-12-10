@extends('layouts.app2')

@section('title', 'Sign In | Reseliency Dashboard')

@section('content')
<div class="text-center mt-2">
    <h5 class="text-primary fs-20">Welcome Back !</h5>
    <p class="text-muted">Sign in to continue to  Reseliency.</p>
</div>
<div class="p-2 mt-4">
    <form method="POST" action="{{ route('login') }}" class="auth-input">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="mb-3">
            <div class="float-end">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-muted">Forgot password?</a>
                @endif
            </div>
            <label class="form-label" for="password-input">Password</label>
            <div class="position-relative auth-pass-inputgroup mb-3">
                <input type="password" class="form-control pe-5 password-input @error('password') is-invalid @enderror" placeholder="Enter password" id="password-input" name="password" required autocomplete="current-password">
                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon h-100" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="remember" id="auth-remember-check" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="auth-remember-check">Remember me</label>
        </div>

        <div class="mt-4">
            <button class="btn btn-primary w-100" type="submit">Sign In</button>
        </div>

        
    </form>

    
</div>
@endsection

@section('right-column')
<div class="col-lg-5">
    <div class="card rounded-0 auth-card bg-primary h-100 border-0 shadow-none p-sm-3 overflow-hidden mb-0">
        <div class="bg-overlay bg-primary"></div>
        <div class="card-body p-4 d-flex justify-content-between flex-column position-relative">
            <div class="auth-image mb-3">
                <img src="{{ asset('assets2/images/logo-light-full.png') }}" alt="" height="26" />
            </div>

            <div class="my-auto">
                <!-- Swiper -->
                <div class="swiper pagination-dynamic-swiper rounded">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="text-center">
                                <h5 class="fs-20 mt-4 text-white mb-0">“I feel confident imposing on myself”
                                </h5>
                                <p class="fs-15 text-white-50 mt-2 pb-4">Vestibulum auctor orci in risus iaculis consequat suscipit felis rutrum aliquet iaculis
                                    augue sed tempus In elementum ullamcorper lectus vitae pretium Nullam ultricies diam
                                    eu ultrices sagittis.</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="text-center">
                                <h5 class="fs-20 mt-4 text-white mb-0">“Our task must be to
                                    free widening circle”</h5>
                                <p class="fs-15 text-white-50 mt-2 pb-4">
                                    Curabitur eget nulla eget augue dignissim condintum Nunc imperdiet ligula porttitor commodo elementum
                                    Vivamus justo risus fringilla suscipit faucibus orci luctus
                                    ultrices posuere cubilia curae ultricies cursus.
                                </p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="text-center">
                                <h5 class="fs-20 mt-4 text-white mb-0">“I've learned that
                                    people forget what you”</h5>
                                <p class="fs-15 text-white-50 mt-2 pb-4">
                                    Pellentesque lacinia scelerisque arcu in aliquam augue molestie rutrum Fusce dignissim dolor id auctor accumsan
                                    vehicula dolor
                                    vivamus feugiat odio erat sed  quis Donec nec scelerisque magna
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination dynamic-pagination"></div>
                </div>
            </div>
            <div class="text-center text-white-75">
                <p class="mb-0">&copy;
                    <script>document.write(new Date().getFullYear())</script> Reseliency
                </p>
            </div>
        </div>
    </div>
</div>
<!--end col-->
@endsection