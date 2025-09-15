@extends('layouts.app')

@section('content')
<div class="container py-5" style="max-width:520px;">
	<div class="card shadow-sm border-0 rounded-4">
		<div class="card-body p-4 p-md-5">
			<h1 class="h4 fw-bold mb-1 text-primary">Sign In</h1>
			<p class="text-muted mb-4 small">Access your dashboard. Use provided role credentials.</p>
			@if($errors->any())
				<div class="alert alert-danger py-2 small">
					{{ $errors->first() }}
				</div>
			@endif
			<form method="POST" action="{{ route('login.perform') }}" novalidate>
				@csrf
				<div class="mb-3">
					<label class="form-label small fw-semibold">Email or Username</label>
					<input type="text" name="login" value="{{ old('login') }}" class="form-control form-control-lg" required autofocus>
				</div>
				<div class="mb-3">
					<label class="form-label small fw-semibold">Password</label>
					<input type="password" name="password" class="form-control form-control-lg" required>
				</div>
				<div class="d-flex justify-content-between align-items-center mb-3 small">
					<div class="form-check">
						<input class="form-check-input" type="checkbox" name="remember" id="remember">
						<label class="form-check-label" for="remember">Remember me</label>
					</div>
					<span class="text-muted">No registration for staff roles</span>
				</div>
				<button class="btn btn-primary w-100 py-2 fw-semibold">Login</button>
			</form>
			<div class="mt-4 small bg-light rounded p-3">
				<strong>Test Credentials</strong>
				<ul class="mb-0 mt-2 ps-3">
					<li>Admin: admin@example.com / Admin!123</li>
					<li>Case Manager: casemgr@example.com / Case!1234</li>
					<li>Attorney: attorney@example.com / Law!12345</li>
					<li>Printing: printing@example.com / Print!1234</li>
					<li>Applicant: applicant@example.com / User!12345</li>
				</ul>
			</div>
		</div>
	</div>
</div>
@endsection

