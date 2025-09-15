<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Login - Horizon Pathways</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<style>
		:root { --hp-primary:#1e40af; --hp-accent:#4f8cff; --hp-bg:#f6f8fc; --hp-text:#1e293b; --hp-muted:#64748b; }
		body { background: var(--hp-bg); color: var(--hp-text); font-family: 'Inter', system-ui, sans-serif; min-height:100vh; display:flex; align-items:center; margin:0; }
		.auth-container { max-width: 480px; margin:0 auto; background:#fff; padding:3rem 2.5rem; border-radius:16px; box-shadow:0 10px 40px rgba(0,0,0,.1); border:1px solid #e5e7eb; margin-top:60px; }
		.auth-header{ text-align:center; margin-bottom:2rem; }
		.auth-header h1{ color:var(--hp-primary); font-weight:700; font-size:1.75rem; margin-bottom:.5rem; }
		.auth-header p{ color:var(--hp-muted); font-size:.95rem; }
		.form-group{ margin-bottom:1.25rem; }
		.form-control{ padding:.85rem 1rem; border:2px solid #e5e7eb; border-radius:8px; font-size:.95rem; transition:.2s; }
		.form-control:focus{ border-color:var(--hp-accent); box-shadow:0 0 0 3px rgba(79,140,255,.1); }
		.btn-primary{ background:var(--hp-primary); border:none; padding:.9rem 1.5rem; border-radius:8px; font-weight:600; font-size:.95rem; width:100%; transition:.2s; }
		.btn-primary:hover{ background:var(--hp-accent); transform:translateY(-1px); }
		.alert-danger{ background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:.9rem 1rem; border-radius:8px; margin-bottom:1.2rem; }
		.auth-links{ text-align:center; margin-top:1.4rem; padding-top:1.2rem; border-top:1px solid #e5e7eb; font-size:.9rem; }
		.auth-links a{ color:var(--hp-primary); font-weight:600; text-decoration:none; }
		.auth-links a:hover{ color:var(--hp-accent); }
		.test-credentials{ background:#f0f9ff; border:1px solid #bfdbfe; border-radius:8px; padding:1rem; margin-top:1rem; text-align:center; font-size:.8rem; }
		.test-credentials h6{ color:var(--hp-primary); margin-bottom:.5rem; font-weight:600; }
	</style>
</head>
<body>
	<div class="container">
		<div class="auth-container">
			<div class="auth-header">
				<h1><i class="bi bi-shield-check me-2"></i>Welcome Back</h1>
				<p>Sign in to your Horizon Pathways account</p>
			</div>
			@if($errors->any())
				<div class="alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
			@endif
			<form method="POST" action="{{ route('login.perform') }}">
				@csrf
				<div class="form-group">
					<input type="text" name="login" class="form-control" placeholder="Email or Username" value="{{ old('login') }}" autofocus required>
				</div>
				<div class="form-group">
					<input type="password" name="password" class="form-control" placeholder="Password" required>
				</div>
				<button type="submit" class="btn btn-primary"><i class="bi bi-box-arrow-in-right me-2"></i>Sign In</button>
			</form>
			<div class="test-credentials">
				<h6><i class="bi bi-info-circle me-1"></i>Demo Accounts</h6>
				<small>Applicant: applicant@example.com / password</small><br>
				<small>Case Manager: case@example.com / password</small>
			</div>
			<div class="auth-links">
				<p>Don't have an account? <a href="/register">Create account</a></p>
				<p><a href="/">← Back to Home</a></p>
			</div>
		</div>
	</div>
</body>
</html>
