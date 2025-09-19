<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ config('app.name', 'Laravel') }}</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="description" content="{{ config('app.name') }} immigration document preparation platform">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/hp.css">
	<style>
		:root { --hp-primary:#1e40af; --hp-accent:#4f8cff; --hp-bg:#f6f8fc; --hp-text:#1e293b; --hp-muted:#64748b; }
	body { background: var(--hp-bg); color: var(--hp-text); font-family: 'Inter', system-ui,-apple-system,"Segoe UI",Roboto,Inter,"Helvetica Neue",Arial,sans-serif; }
		a { text-decoration:none; }
		a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible { outline: 3px solid var(--hp-accent); outline-offset:2px; }
		.skip-link { position:absolute; left:-999px; top:auto; width:1px; height:1px; overflow:hidden; }
		.skip-link:focus { position:static; width:auto; height:auto; padding:.5rem 1rem; background:#fff; z-index:1000; }
	.main-header { background:#fff !important; border-bottom:1px solid #e5e7eb; box-shadow:0 2px 8px 0 rgba(80,80,200,.04); padding: 0.2rem 0; }
	.top-header { background: #193d91ff; color: #fff; padding: 0.30rem 0; }
	.top-header .top-header-icon, .top-header .top-header-phone, .top-header .top-header-login { color: #fff !important; }
	.top-header .top-header-icon:hover { color: var(--hp-accent) !important; }
	.main-header, .main-header * { font-family: "Segoe UI", "Inter", system-ui, -apple-system, Roboto, "Helvetica Neue", Arial, sans-serif !important; }
	.main-header .navbar-brand { font-weight:400; font-size:1.45rem; color:var(--hp-primary)!important; letter-spacing:.5px; display:flex; align-items:center; }
	.main-header .navbar-brand img { height:56px; margin-right:12px; }
	.main-header .nav-link {
		color: var(--hp-text)!important;
		font-weight: 400;
		margin-right: 1.1rem;
		position: relative;
		padding: .6rem .4rem;
		transition: color 0.2s;
	}
	.main-header .nav-link:after {
		content: "";
		position: absolute;
		left: 50%;
		transform: translateX(-50%) scaleX(0);
		bottom: -10px;
		width: 80%;
		height: 1px;
		background: var(--hp-accent);
		border-radius: 1px;
		transition: transform 0.3s cubic-bezier(.4,0,.2,1);
	}
	.main-header .nav-link:hover {
		color: var(--hp-accent)!important;
	}
	.main-header .nav-link:hover:after {
		transform: translateX(-50%) scaleX(1);
	}
		main { min-height:60vh; }
	.main-footer {
		background: linear-gradient(180deg, #1b2c58 0%, #23272f 100%);
		border-top: none;
		padding: 2.5rem 1.1rem 1.1rem 1.1rem;
		color: #fff;
		font-size: 0.98rem;
		margin-top: 0.7rem;
		box-shadow: 0 2px 16px 0 rgba(0,0,0,0.08);
		min-height: 380px;
	}
	.main-footer .footer-row {
		display: flex;
		justify-content: space-between;
		align-items: flex-start;
		max-width: 1100px;
		margin: 0 auto;
		padding: 2.2rem 0.7rem 0 0.7rem;
		gap: 1.1rem;
	}
	.main-footer .footer-col {
		flex: 1 1 0;
		min-width: 180px;
	}
	   .main-footer .footer-logo img {
		   height: 56px;
		   background: #fff;
		   border-radius: 10px;
		   padding: 0.15rem 0.7rem;
		   margin-bottom: 1.5rem;
	   }
	.main-footer .footer-disclaimer {
		font-size: 0.98rem;
		color: #fff;
		margin-bottom: 0.4rem;
		margin-top: 0.1rem;
		line-height: 1.3;
		max-width: 340px;
	}
	.main-footer .footer-title {
		font-size: 1.1rem;
		font-weight: 700;
		margin-bottom: 0.5rem;
		color: #fff;
	}
	.main-footer .footer-links {
		padding-left: 0;
		margin-bottom: 0.08rem;
		list-style: none;
	}
	.main-footer .footer-links li {
		margin-bottom: 0.3rem;
		font-size: 0.98rem;
	}
	.main-footer .footer-links a {
		color: #fff;
		text-decoration: none;
		transition: color 0.2s;
		position: relative;
		padding-left: 0.7rem;
	}
	.main-footer .footer-links a:before {
		content: '\203A';
		color: #ff3c2a;
		position: absolute;
		left: 0;
		font-size: 1em;
		top: 0;
	}
	.main-footer .footer-links a:hover {
		color: #ff3c2a;
		text-decoration: underline;
	}
	.main-footer .footer-contact {
		font-size: 0.98rem;
		margin-bottom: 0.3rem;
		color: #fff;
		display: flex;
		align-items: center;
		gap: 0.4rem;
	}
	.main-footer .footer-contact i {
		color: #ff3c2a;
		font-size: 1em;
		min-width: 1em;
		text-align: center;
	}
	.main-footer .footer-social {
		margin-top: 0.5rem;
	}
	.main-footer .footer-social a {
		margin: 0 0.08rem;
		color: #ff3c2a;
		font-size: 1.3rem;
		transition: color 0.2s;
		vertical-align: middle;
	}
	.main-footer .footer-social a:hover {
		color: #fff;
	}
	.main-footer .footer-divider {
		border: none;
		border-top: 2px solid #da1604ff;
		margin: 0.7rem 0 0.5rem 0;
	}
	.main-footer .footer-bottom {
		display: flex;
		flex-wrap: wrap;
		justify-content: space-between;
		align-items: center;
		font-size: 0.98rem;
		gap: 0.2rem;
		max-width: 1100px;
		margin: 0 auto;
		padding: 0 0.7rem;
		color: #fff;
	}
	.main-footer .footer-bottom a {
		color: #fff;
		margin-left: 0.7rem;
		text-decoration: none;
		transition: color 0.2s;
	}
	.main-footer .footer-bottom a:hover {
		color: #ff3c2a;
		text-decoration: underline;
	}
	</style>
	@stack('head')
</head>
<body>
	<a href="#main-content" class="skip-link">Skip to content</a>
	<div class="top-header">
		<div class="container d-flex justify-content-between align-items-center">
			<div class="top-header-left">
				<a href="https://facebook.com/horizonpathways" target="_blank" class="top-header-icon" title="Facebook"><i class="bi bi-facebook"></i></a>
				<a href="https://twitter.com/horizonpathways" target="_blank" class="top-header-icon" title="Twitter"><i class="bi bi-twitter"></i></a>
				<a href="#" class="top-header-icon" title="Instagram"><i class="bi bi-instagram"></i></a>
			</div>
			<div class="top-header-right">
				<span class="top-header-phone">+1 (800) 795 7153</span>
				<a href="/login" class="top-header-login ms-3">Log In</a>
			</div>
		</div>
	</div>
	<header class="main-header" role="banner">
		<nav class="navbar navbar-expand-lg navbar-light bg-white" aria-label="Main navigation">
			<div class="container">
				<a class="navbar-brand" href="/">
					<img src="/images/logo.png" alt="Horizon Pathways logo">
				</a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="mainNavbar">
					<ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
						
						<li class="nav-item"><a class="nav-link {{ request()->is('how-it-works') ? 'active' : '' }}" href="https://horizonpathways.us/how-it-works/">How It Works</a></li>
						<li class="nav-item"><a class="nav-link {{ request()->is('about') ? 'active' : '' }}" href="https://horizonpathways.us/about/">About</a></li>
						<li class="nav-item"><a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">Quiz</a></li>
						<li class="nav-item"><a class="nav-link {{ request()->is('news') ? 'active' : '' }}" href="https://horizonpathways.us/news-blog/">News & Blog</a></li>
						<li class="nav-item"><a class="nav-link {{ request()->is('faq') ? 'active' : '' }}" href="https://horizonpathways.us/faq/">FAQ</a></li>
						<li class="nav-item"><a class="nav-link {{ request()->is('contact') ? 'active' : '' }}" href="https://horizonpathways.us/contact/">Contact</a></li>
						<li class="nav-item d-lg-none"><a class="nav-link {{ request()->is('register') ? 'active' : '' }}" href="/register">Register</a></li>
						<li class="nav-item d-lg-none"><a class="nav-link {{ request()->is('login') ? 'active' : '' }}" href="/login">Login</a></li>
						@auth
						@php
							try { $unread = \App\Models\Notification::where('user_id',auth()->id())->whereNull('read_at')->count(); }
							catch(\Throwable $e) { $unread = 0; }
						@endphp
						<li class="nav-item ms-lg-3 position-relative">
							<a class="nav-link" href="#" aria-label="Notifications">
								<i class="bi bi-bell" style="font-size:1.25rem; position:relative;">
									@if(($unread ?? 0) > 0)
									<span style="position:absolute;top:-6px;right:-10px;background:#dc2626;color:#fff;font-size:.55rem;padding:2px 5px;border-radius:10px;line-height:1;font-weight:600;">{{ $unread }}</span>
									@endif
								</i>
							</a>
						</li>
						<li class="nav-item ms-lg-2">
							<a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-sm btn-outline-primary">Logout</a>
						</li>
						@endauth
					</ul>
				</div>
			</div>
		</nav>
	</header>
	<main id="main-content" role="main">
		@yield('content')
	</main>
	<footer class="main-footer" role="contentinfo">
		<div class="footer-row">
			<div class="footer-col">
				<div class="footer-logo">
					<img src="/images/logo.png" alt="Horizon Pathways logo">
				</div>
				<div class="footer-disclaimer">
					Horizon Pathways is a private document preparation service and is not a law firm, and is not affiliated with the U.S. Citizenship and Immigration Services (USCIS) or any government agency.
				</div>
			</div>
			<div class="footer-col">
				<div style="padding-top:1.2rem;"></div>
				<div class="footer-title">Quick Links</div>
				<ul class="footer-links">
					<li><a href="/">Home</a></li>
					<li><a href="/how-it-works">How It Works</a></li>
					<li><a href="/about">About</a></li>
					<li><a href="/news">News</a></li>
					<li><a href="/faq">FAQ</a></li>
					<li><a href="/contact">Contact</a></li>
				</ul>
			</div>
			<div class="footer-col">
				<div class="footer-title">Contact</div>
				<div class="footer-contact"><i class="bi bi-geo-alt-fill"></i>7375 Executive Pl, Ste 400 #1062 Lanham, MD 20706</div>
				<div class="footer-contact"><i class="bi bi-envelope-fill"></i>support@horizonpathways.us</div>
				<div class="footer-contact"><i class="bi bi-telephone-fill"></i>+1 (800) 795 7153</div>
				<div class="footer-contact"><i class="bi bi-clock-fill"></i>Mon–Fri, 9am–6pm EST</div>
				<div class="footer-social" aria-label="Social media links">
					<a href="https://facebook.com/horizonpathways" target="_blank" rel="noopener" title="Facebook"><i class="bi bi-facebook" aria-hidden="true"></i></a>
					<a href="https://twitter.com/horizonpathways" target="_blank" rel="noopener" title="Twitter"><i class="bi bi-twitter" aria-hidden="true"></i></a>
					<a href="https://youtube.com/@horizonpathways" target="_blank" rel="noopener" title="YouTube"><i class="bi bi-youtube" aria-hidden="true"></i></a>
				</div>
			</div>
		</div>
		<hr class="footer-divider">
		<div class="footer-bottom">
			<div>&copy; {{ date('Y') }} Horizon Pathways LLC. All rights reserved.</div>
			<div>
				<a href="/terms-and-conditions">Terms & Conditions</a>
				<a href="/privacy-policy">Privacy Policy</a>
			</div>
		</div>
	</footer>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	@stack('scripts')
	
	<!-- Logout Form -->
	<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
		@csrf
	</form>
</body>
</html>
