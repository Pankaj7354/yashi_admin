<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="{{asset('adminlte/dist/css/adminlte.css')}}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="register-page bg-body-secondary">
    <div class="register-box">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <a href="#" class="link-dark text-center link-offset-2 link-opacity-100 link-opacity-50-hover">
                    <h1 class="mb-0">
                        <b>Registration</b>
                    </h1>
                </a>
            </div>
            <div class="card-body register-card-body">

               <!-- Include jQuery CDN if not already included -->


<form id="registerForm" action="{{ route('auth.register') }}" method="post">
    @csrf

    <!-- Full Name -->
    <div class="input-group mb-1">
        <div class="form-floating">
            <input id="registerFullName" name="name" type="text" class="form-control" placeholder="" required>
            <label for="registerFullName">Full Name</label>
        </div>
        <div class="input-group-text"> <span class="bi bi-person"></span> </div>
    </div>
    @error('name')
        <span class="text-danger">{{ $message }}</span>
    @enderror

    <!-- Email -->
    <div class="input-group mb-1">
        <div class="form-floating">
            <input id="registerEmail" name="email" type="email" class="form-control" placeholder="" required>
            <label for="registerEmail">Email</label>
        </div>
        <div class="input-group-text"> <span class="bi bi-envelope"></span> </div>
    </div>
    @error('email')
        <span class="text-danger">{{ $message }}</span>
    @enderror

    <!-- Verify Email Button -->
    <div class="mb-2">
        <button type="button" id="sendOtpBtn" class="btn btn-info">Send OTP</button>
    </div>

    <!-- OTP Input -->
    <div class="input-group mb-1" id="otpSection" style="display: none;">
        <div class="form-floating">
            <input id="otp" name="otp" type="text" class="form-control" placeholder="Enter OTP" required>
            <label for="otp">OTP</label>
        </div>
        <div class="input-group-text"> <span class="bi bi-key"></span> </div>
    </div>

    <!-- Password -->
    <div class="input-group mb-1">
        <div class="form-floating">
            <input id="registerPassword" name="password" type="password" class="form-control" placeholder="" required>
            <label for="registerPassword">Password</label>
        </div>
        <div class="input-group-text"> <span class="bi bi-lock-fill"></span> </div>
    </div>
    @error('password')
        <span class="text-danger">{{ $message }}</span>
    @enderror

    <!-- Confirm Password -->
    <div class="input-group mb-1">
        <div class="form-floating">
            <input id="registerPasswordConfirm" name="password_confirmation" type="password" class="form-control" placeholder="" required>
            <label for="registerPasswordConfirm">Confirm Password</label>
        </div>
        <div class="input-group-text"> <span class="bi bi-lock-fill"></span> </div>
    </div>

    <!-- Submit -->
    <div class="row">
        <div class="col-12">
            <div class="d-grid gap-12">
                <button type="submit" id="submitFormButton" class="btn btn-primary" disabled>Sign Up</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('#sendOtpBtn').on('click', function () {
            let email = $('#registerEmail').val();

            if (!email) {
                alert('Please enter an email first.');
                return;
            }

            $.ajax({
                url: '{{ route("auth.sendOtp") }}',
                type: 'POST',
                dataType: 'json',
                data: JSON.stringify({ email: email }),
                contentType: 'application/json',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 'success') {
                        alert('OTP sent to your email!');
                        $('#otpSection').show();
                        $('#submitFormButton').prop('disabled', false);
                    } else {
                        alert(response.message || 'Failed to send OTP. Please try again.');
                    }
                },
                error: function (xhr) {
                    let res = xhr.responseJSON;
                    alert(res.message || 'Something went wrong.');
                }
            });
        });
    });
</script>

                
                

                <div class="social-auth-links text-center mb-3 d-grid gap-2">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-danger">
                        <i class="bi bi-google me-2"></i> Sign in using Google+
                    </a>
                </div>

                <p class="mb-0">
                    <a href="{{ route('auth.login') }}" class="link-primary text-center">
                        I already have a membership, login
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script> <!-- Laravel specific script file -->
</body>
</html>
