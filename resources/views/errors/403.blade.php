<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Unauthorized Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="text-center">
        <div class="mb-4 text-danger" style="font-size: 6rem;">
            <i class="bi bi-shield-lock-fill"></i>
        </div>
        <h1 class="display-4 fw-bold">403</h1>
        <h3 class="fw-semibold mb-3">Unauthorized Access</h3>
        <p class="text-secondary mb-4">You do not have permission to access this resource.<br>Please contact the school administrator if you believe this is an error.</p>
        <a href="{{ url()->previous() }}" class="btn btn-outline-light rounded-pill me-2">
            <i class="bi bi-arrow-left me-1"></i> Go Back
        </a>
        <a href="{{ route('login') }}" class="btn btn-primary rounded-pill">
            <i class="bi bi-house me-1"></i> Go to Login
        </a>
    </div>
</body>
</html>
