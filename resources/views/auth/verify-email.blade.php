<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify Email - Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            --dark-bg: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .verify-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            background: #ffffff;
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        .verify-header {
            background: var(--primary-gradient);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .icon-wrapper {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .verify-body {
            padding: 40px 30px;
        }
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            transition: transform 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }
        .alert-success {
            background: #d1fae5;
            border-color: #6ee7b7;
            color: #065f46;
        }
        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #3b82f6;
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .check-email-icon {
            font-size: 5rem;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="verify-header">
            <div class="icon-wrapper">
                <i class="bi bi-envelope-check check-email-icon"></i>
            </div>
            <h3 class="mb-2 fw-bold">Verify Your Email Address</h3>
            <p class="mb-0 opacity-75">One more step to get started!</p>
        </div>

        <div class="verify-body">
            @if (session('verified'))
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Success!</strong> Your email has been verified. You can now access all features.
                    </div>
                </div>
            @endif

            @if (session('message'))
                <div class="alert alert-success d-flex align-items-center mb-4" role="alert">
                    <i class="bi bi-send-check me-2 fs-5"></i>
                    <div>{{ session('message') }}</div>
                </div>
            @endif

            <div class="text-center mb-4">
                <h5 class="mb-3">Welcome, {{ auth()->user()->name }}! 👋</h5>
                <p class="text-muted mb-0">
                    Before you can access the system, we need to verify your email address 
                    <strong class="text-primary">{{ auth()->user()->email }}</strong>
                </p>
            </div>

            <div class="info-box">
                <div class="d-flex align-items-start">
                    <i class="bi bi-info-circle text-primary me-2 fs-5"></i>
                    <div>
                        <strong class="d-block mb-1">Check Your Inbox</strong>
                        <small class="text-muted">
                            We've sent a verification email to your address. 
                            Please click the verification link in the email to continue.
                        </small>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-3 mt-4">
                <form method="POST" action="{{ route('verification.send') }}" id="resendForm">
                    @csrf
                    <button type="submit" class="btn btn-primary w-100 rounded-pill" id="resendBtn">
                        <i class="bi bi-send me-2"></i>
                        <span id="btnText">Resend Verification Email</span>
                        <span id="btnLoading" class="d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Sending...
                        </span>
                    </button>
                </form>

                <div class="text-center">
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>
                        Didn't receive the email? Check your spam folder or click resend above.
                    </small>
                </div>
            </div>

            <hr class="my-4">

            <div class="text-center">
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-muted text-decoration-none">
                        <i class="bi bi-box-arrow-left me-1"></i>
                        Logout and try different account
                    </button>
                </form>
            </div>
        </div>

        <div class="bg-light px-4 py-3 border-top text-center">
            <small class="text-muted">
                <i class="bi bi-shield-check me-1"></i>
                Email verification helps keep your account secure
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle resend button state
        document.getElementById('resendForm').addEventListener('submit', function() {
            const btn = document.getElementById('resendBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            
            btn.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            
            // Re-enable after 3 seconds
            setTimeout(function() {
                btn.disabled = false;
                btnText.classList.remove('d-none');
                btnLoading.classList.add('d-none');
            }, 3000);
        });

        // Auto-hide success messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>
