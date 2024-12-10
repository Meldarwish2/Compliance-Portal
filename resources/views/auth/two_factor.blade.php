@extends('layouts.app2')

@section('title', 'Two Step Verification | Reseliency Dashboard')

@section('content')
<div class="mb-4 text-center">
    <lord-icon src="https://cdn.lordicon.com/diihvcfp.json" trigger="loop" class="avatar-md"> </lord-icon>
</div>

<div class="p-2 mt-4">
    <div class="text-muted text-center mb-4 mx-lg-3">
        <h4>Verify Your Email</h4>
        <p>Please enter the 6-digit code sent to <span class="fw-semibold">{{Auth::user()->email}}</span></p>
    </div>
    @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          @endif
    <!-- OTP Form -->
    <form autocomplete="off" id="otp-form" method="POST" action="{{ route('verify.2fa') }}">
        @csrf
        <div class="row" id="otp-container">
            @for ($i = 1; $i <= 6; $i++)
                <div class="col-2">
                    <div class="mb-3">
                        <label for="digit{{ $i }}-input" class="visually-hidden">Digit {{ $i }}</label>
                        <input type="text" class="form-control form-control-lg bg-light border-light text-center otp-input" maxLength="1" id="digit{{ $i }}-input">
                    </div>
                </div>
            @endfor
        </div>

        <!-- Hidden input to store OTP -->
        <input type="hidden" name="otp" id="otp">
        @error('otp')
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @enderror
    </form>

    <div class="mt-3">
        <button type="button" class="btn btn-primary w-100" id="confirm-btn">Confirm</button>
    </div>
</div>
<div class="mt-4 text-center">
    <p class="mb-0">Didn't receive a code? <a href="auth-pass-reset-basic.html" class="fw-semibold text-primary text-decoration-underline">Resend</a></p>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.otp-input');
        
        // Add input event listeners to handle OTP field focus and key events
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                if (value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });

            input.addEventListener('paste', (e) => {
                const paste = e.clipboardData.getData('text');
                const pasteDigits = paste.substring(0, 6).split('');
                pasteDigits.forEach((digit, i) => {
                    if (inputs[i]) {
                        inputs[i].value = digit;
                    }
                });
                inputs[pasteDigits.length - 1]?.focus();
                e.preventDefault();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        // Event listener for the Confirm button
        document.getElementById('confirm-btn').addEventListener('click', function() {
            const otp = Array.from(inputs).map(input => input.value).join('');

            // Check if OTP has 6 digits
            if (otp.length === 6) {
                // Insert the OTP into the hidden input
                let otpInput = document.getElementById('otp');
                otpInput.value = otp;

                // Submit the form
                document.getElementById('otp-form').submit();
            } else {
                alert('Please enter a valid 6-digit OTP.');
            }
        });
    });
</script>
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
                                <h5 class="fs-20 mt-4 text-white mb-0">“I feel confident imposing on myself”</h5>
                                <p class="fs-15 text-white-50 mt-2 pb-4">Vestibulum auctor orci in risus iaculis consequat suscipit felis rutrum aliquet iaculis
                                    augue sed tempus In elementum ullamcorper lectus vitae pretium Nullam ultricies diam
                                    eu ultrices sagittis.</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="text-center">
                                <h5 class="fs-20 mt-4 text-white mb-0">“Our task must be to free widening circle”</h5>
                                <p class="fs-15 text-white-50 mt-2 pb-4">
                                    Curabitur eget nulla eget augue dignissim condintum Nunc imperdiet ligula porttitor commodo elementum
                                    Vivamus justo risus fringilla suscipit faucibus orci luctus
                                    ultrices posuere cubilia curae ultricies cursus.
                                </p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="text-center">
                                <h5 class="fs-20 mt-4 text-white mb-0">“I've learned that people forget what you”</h5>
                                <p class="fs-15 text-white-50 mt-2 pb-4">
                                    Pellentesque lacinia scelerisque arcu in aliquam augue molestie rutrum Fusce dignissim dolor id auctor accumsan
                                    vehicula dolor vivamus feugiat odio erat sed quis Donec nec scelerisque magna
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-pagination dynamic-pagination"></div>
                </div>
            </div>
            <div class="text-center text-white-75">
                <p class="mb-0">&copy;
                    <script>
                        document.write(new Date().getFullYear())
                    </script> Reseliency
                </p>
            </div>
        </div>
    </div>
</div>
<!--end col-->
@endsection
