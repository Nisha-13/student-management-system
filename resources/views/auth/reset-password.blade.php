<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Student Management System</title>
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
        .reset-card {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            background: #ffffff;
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .reset-header {
            background: #0f172a;
            color: #ffffff;
            padding: 36px 24px 28px;
            text-align: center;
        }
        .reset-header .icon-wrapper {
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
        .password-toggle {
            cursor: pointer;
        }
        .password-strength {
            height: 4px;
            border-radius: 2px;
            background: #e5e7eb;
            margin-top: 8px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <div class="reset-header">
            <div class="icon-wrapper">
                <i class="bi bi-shield-lock-fill fs-2 text-white"></i>
            </div>
            <h4 class="fw-bold mb-1">Set New Password</h4>
            <p class="text-white-50 small mb-0">Create a strong password</p>
        </div>

        <div class="p-4">
            @if($errors->any())
                <div class="alert alert-danger p-3 small mb-4">
                    <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Error</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('password.update') }}" method="POST" id="resetForm">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="email" class="form-label fw-medium small">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                        <input 
                            type="email" 
                            class="form-control bg-light" 
                            id="email" 
                            name="email" 
                            value="{{ $email ?? old('email') }}"
                            required 
                            autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-medium small">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                        <input 
                            type="password" 
                            class="form-control bg-light" 
                            id="password" 
                            name="password" 
                            placeholder="Minimum 8 characters"
                            required
                            oninput="checkPasswordStrength()">
                        <span class="input-group-text bg-light password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                            <i class="bi bi-eye" id="toggleIcon1"></i>
                        </span>
                    </div>
                    <div class="password-strength">
                        <div class="password-strength-bar" id="strengthBar"></div>
                    </div>
                    <small class="text-muted" id="strengthText"></small>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label fw-medium small">Confirm Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="bi bi-lock-fill"></i></span>
                        <input 
                            type="password" 
                            class="form-control bg-light" 
                            id="password_confirmation" 
                            name="password_confirmation" 
                            placeholder="Re-enter password"
                            required>
                        <span class="input-group-text bg-light password-toggle" onclick="togglePassword('password_confirmation', 'toggleIcon2')">
                            <i class="bi bi-eye" id="toggleIcon2"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-3 mb-3">
                    <i class="bi bi-check-circle me-2"></i>Reset Password
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
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('bi-eye');
                toggleIcon.classList.add('bi-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('bi-eye-slash');
                toggleIcon.classList.add('bi-eye');
            }
        }

        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('strengthBar');
            const strengthText = document.getElementById('strengthText');
            
            let strength = 0;
            let text = '';
            let color = '';
            
            if (password.length >= 8) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    text = 'Very Weak';
                    color = '#ef4444';
                    break;
                case 2:
                    text = 'Weak';
                    color = '#f97316';
                    break;
                case 3:
                    text = 'Medium';
                    color = '#eab308';
                    break;
                case 4:
                    text = 'Strong';
                    color = '#22c55e';
                    break;
                case 5:
                    text = 'Very Strong';
                    color = '#10b981';
                    break;
            }
            
            strengthBar.style.width = (strength * 20) + '%';
            strengthBar.style.backgroundColor = color;
            strengthText.textContent = text;
            strengthText.style.color = color;
        }
    </script>
</body>
</html>
