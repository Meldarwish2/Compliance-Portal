<!DOCTYPE html>
<html>
<head>
    <title>Two-Factor Authentication</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Two-Factor Authentication</h4>
                </div>
                <div class="card-body">
                    <p class="text-center">Enter the 2FA code sent to your email.</p>
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('verify.2fa') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="two_factor_code" class="form-label">2FA Code</label>
                            <input type="text" name="two_factor_code" id="two_factor_code" class="form-control" required autofocus>
                            @error('two_factor_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Verify Code</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
