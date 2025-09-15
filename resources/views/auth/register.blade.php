<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register - Horizon Pathways</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<style>
		:root { --hp-primary:#1e40af; --hp-accent:#4f8cff; --hp-bg:#f6f8fc; --hp-text:#1e293b; --hp-muted:#64748b; }
		body { background: var(--hp-bg); color: var(--hp-text); font-family: 'Inter', system-ui, sans-serif; min-height:100vh; padding:2rem 0; margin:0; }
		.auth-container { max-width: 550px; margin:0 auto; background:#fff; padding:3rem 2.5rem; border-radius:16px; box-shadow:0 10px 40px rgba(0,0,0,.1); border:1px solid #e5e7eb; margin-top:40px; }
		.auth-header{ text-align:center; margin-bottom:2rem; }
		.auth-header h1{ color:var(--hp-primary); font-weight:700; font-size:1.75rem; margin-bottom:.5rem; }
		.auth-header p{ color:var(--hp-muted); font-size:.95rem; }
		.form-group{ margin-bottom:1.1rem; }
		.form-row{ display:flex; gap:1rem; }
		.form-row .form-group{ flex:1; margin-bottom:1.1rem; }
		.form-control{ padding:.8rem 1rem; border:2px solid #e5e7eb; border-radius:8px; font-size:.95rem; transition:.2s; width:100%; }
		.form-control:focus{ border-color:var(--hp-accent); box-shadow:0 0 0 3px rgba(79,140,255,.1); }
		.btn-primary{ background:var(--hp-primary); border:none; padding:.85rem 1.5rem; border-radius:8px; font-weight:600; font-size:.95rem; width:100%; transition:.2s; }
		.btn-primary:hover{ background:var(--hp-accent); transform:translateY(-1px); }
		.alert-danger{ background:#fef2f2; border:1px solid #fecaca; color:#dc2626; padding:.9rem 1rem; border-radius:8px; margin-bottom:1.2rem; }
		.auth-links{ text-align:center; margin-top:1.4rem; padding-top:1.2rem; border-top:1px solid #e5e7eb; font-size:.9rem; }
		.auth-links a{ color:var(--hp-primary); font-weight:600; text-decoration:none; }
		.auth-links a:hover{ color:var(--hp-accent); }
		.password-requirements{ background:#f0f9ff; border:1px solid #bfdbfe; border-radius:8px; padding:.65rem .75rem; font-size:.75rem; color:var(--hp-muted); margin-top:.4rem; }
	</style>
</head>
<body>
	<div class="container">
		<div class="auth-container">
			<div class="auth-header">
				<h1><i class="bi bi-person-plus me-2"></i>Create Account</h1>
				<p>Join Horizon Pathways to start your immigration journey</p>
			</div>
			@if($errors->any())
				<div class="alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
			@endif
			@php($pkg = request('pkg'))
			<form method="POST" action="{{ route('register.perform') }}">
				@csrf
				<input type="hidden" name="vt" value="{{ request('vt') }}">
				@if($pkg)
				<input type="hidden" name="pkg" value="{{ $pkg }}">
				@endif
				<div class="form-group">
					<input type="text" name="username" class="form-control" placeholder="Username" value="{{ old('username') }}" required>
				</div>
				<div class="form-row">
					<div class="form-group">
						<input type="text" name="first_name" class="form-control" placeholder="First Name" value="{{ old('first_name') }}" required>
					</div>
					<div class="form-group">
						<input type="text" name="last_name" class="form-control" placeholder="Last Name" value="{{ old('last_name') }}" required>
					</div>
				</div>
				<div class="form-group">
					<input type="email" name="email" class="form-control" placeholder="Email Address" value="{{ old('email') }}" required>
				</div>
				<div class="form-group">
					<input type="password" name="password" class="form-control" placeholder="Password" required>
					<div class="password-requirements"><i class="bi bi-info-circle me-1"></i>Password must be at least 8 characters</div>
				</div>
				<div class="form-group">
					<input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required>
				</div>
				<button type="submit" class="btn btn-primary"><i class="bi bi-person-check me-2"></i>Create Account & Continue</button>
			</form>
				@if($pkg)
					<div style="margin-top:1rem;font-size:.8rem;color:#1e293b;background:#f1f5f9;border:1px solid #e2e8f0;padding:.75rem 1rem;border-radius:8px;">
						<strong>Selected Package ID:</strong> {{ $pkg }} (final details after signup)
					</div>
				@endif
			<div class="auth-links">
				<p>Already have an account? <a href="/login">Sign in here</a></p>
				<p><a href="/">← Back to Home</a></p>
			</div>
		</div>
	</div>
</body>
</html>
