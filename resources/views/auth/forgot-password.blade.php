<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Student Management System</title>
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
            margin: 0;
            padding: 20px;
        }
        .forgot-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            background: #ffffff;
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .forgot-header {
            background: #0f172a;
            color: #ffffff;
            padding: 36px 24px 28px;
            text-align: center;
        }
        .forgot-header .icon-wrapper {
            width: 64px;
            height: 64px;
            background: var(--primary-gradient);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
        }
        .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }
        .btn-primary {
            background: var(--primary-gradient);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="forgot-card">
        <div class="forgot-header">
            <div class="icon-wrapper">
                <i class="bi bi-key-fill fs-2 text-white"></i>
            </div>
            <h4 class="fw-bold mb-1">Reset Password</h4>
            <p class="text-white-50 small mb-0">We'll send you a reset link</p>
        </div>

        <div class="p-4">
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show p-3 small mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger p-3 small mb-4">
                    @foreach($errors->all() as $error)
                        <div><i class="bi bi-exclamation-circle me-2"></i>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <div class="alert alert-info small mb-4">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Note:</strong> This is for Admin users only. Teachers and Students should use the <a href="{{ route('portal.request-link.form') }}" class="alert-link">Portal Link Request</a> instead.
            </div>

            <form action="{{ route('password.email') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label fw-medium small">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input 
                            type="email" 
                            class="form-control bg-light" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            placeholder="admin@school.com" 
                            required 
                            autofocus>
                    </div>
                    <div class="form-text">Enter your registered email address</div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-3 mb-3">
                    <i class="bi bi-send me-2"></i>Send Reset Link
                </button>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-decoration-none small">
                        <i class="bi bi-arrow-left me-1"></i>Back to Login
                    </a>
                </div>
            </form>
        </div>

        <div class="bg-light px-4 py-3 border-top text-center">
            <p class="small text-muted mb-0">&copy; {{ date('Y') }} Student Management System</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
