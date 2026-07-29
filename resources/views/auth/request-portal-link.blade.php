<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Portal Access Link - Student Management System</title>
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
        .card-box {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4);
            background: #ffffff;
            overflow: hidden;
            width: 100%;
            max-width: 420px;
        }
        .card-header-block {
            background: #0f172a;
            color: #ffffff;
            padding: 36px 24px 28px;
            text-align: center;
        }
        .card-header-block .icon-wrapper {
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
            letter-spacing: 0.3px;
            transition: opacity 0.2s ease, transform 0.1s ease;
        }
        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .input-group-text {
            color: #64748b;
        }
        .back-link {
            color: #6366f1;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s;
        }
        .back-link:hover {
            color: #4f46e5;
            text-decoration: underline;
        }
        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 0.82rem;
            color: #0369a1;
        }
    </style>
</head>
<body>
    <div class="card-box">

        {{-- Header --}}
        <div class="card-header-block">
            <div class="icon-wrapper">
                <i class="bi bi-envelope-open-fill fs-2 text-white"></i>
            </div>
            <h4 class="fw-bold mb-1">Request Access Link</h4>
            <p class="text-white-50 small mb-0">Get a new portal login link sent to your email</p>
        </div>

        <div class="p-4">

            {{-- Success Message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show p-3 small mb-4 rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger p-3 small mb-4 rounded-3">
                    <div class="fw-semibold mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Please fix the following:
                    </div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Throttle Error --}}
            @error('email')
                @if(str_contains($message, 'Too Many'))
                    <div class="alert alert-warning p-3 small mb-4 rounded-3">
                        <i class="bi bi-clock-fill me-2"></i>
                        Too many attempts. Please wait a minute before trying again.
                    </div>
                @endif
            @enderror

            {{-- Info box --}}
            <div class="info-box mb-4">
                <i class="bi bi-info-circle-fill me-2"></i>
                Enter your registered email address. If it exists in our system, a
                <strong>new 48-hour access link</strong> will be sent to your inbox.
                This is only available for <strong>students</strong> and <strong>teachers</strong>.
            </div>

            {{-- Form --}}
            <form action="{{ route('portal.request-link') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="email" class="form-label fw-medium small text-dark">Your Registered Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input
                            type="email"
                            class="form-control border-start-0 bg-light @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="name@school.com"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 rounded-3 shadow-sm">
                    <i class="bi bi-send-fill me-2"></i>Send Me an Access Link
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <div class="bg-light px-4 py-3 border-top text-center">
            <a href="{{ route('login') }}" class="back-link">
                <i class="bi bi-arrow-left me-1"></i>Back to Login
            </a>
            <p class="small text-muted mb-0 mt-2">
                &copy; {{ date('Y') }} Student Management System. All rights reserved.
            </p>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
