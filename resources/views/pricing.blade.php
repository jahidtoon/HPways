@extends('layouts.app')

@section('content')
<style>
.pricing-banner {
    position: relative;
    background:
        linear-gradient(120deg, rgba(255,221,51,0.85) 0%, rgba(255,221,51,0.75) 100%),
        url('/images/pricing_banner.webp') center center / cover no-repeat;
    background-size: cover;
    background-position: center;
    padding: 6rem 0 5rem 0;
    margin-bottom: 5rem;
    overflow: hidden;
    border-bottom-left-radius: 30% 5%;
    border-bottom-right-radius: 30% 5%;
}
.pricing-banner .banner-photo-bg {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    object-fit: cover;
    opacity: 0.32;
    z-index: 1;
    pointer-events: none;
}
.banner-content { position: relative; z-index: 2; }
@keyframes gradientAnimation {0%{background-position:0% 50%;}50%{background-position:100% 50%;}100%{background-position:0% 50%;}}
@keyframes float {0%{transform:translateY(0px);}50%{transform:translateY(-10px);}100%{transform:translateY(0px);}}
.pricing-banner {background: #696a9dff; background-size: cover; animation: none; padding:5rem 0; margin-bottom:5rem; position:relative; overflow:hidden; border-bottom-left-radius:30% 5%; border-bottom-right-radius:30% 5%;}
.pricing-banner::before,.pricing-banner::after{content:"";position:absolute;background:rgba(255,255,255,0.1);border-radius:50%;}
.pricing-banner::before{width:600px;height:600px;top:-300px;right:-100px;}
.pricing-banner::after{width:500px;height:500px;bottom:-250px;left:-150px;}
.banner-shape{position:absolute;opacity:0.15;}
.shape-1{top:20%;left:10%;width:60px;height:60px;border-radius:12px;background:white;transform:rotate(45deg);animation:float 6s ease-in-out infinite;}
.shape-2{bottom:20%;right:10%;width:80px;height:80px;border-radius:50%;background:white;animation:float 8s ease-in-out infinite;}
.shape-3{top:50%;right:20%;width:40px;height:40px;border-radius:8px;background:white;animation:float 7s ease-in-out infinite;}
.pricing-banner h1{color:white;font-size:3.5rem;font-weight:800;margin-bottom:1.2rem;text-transform:uppercase;letter-spacing:1px;text-shadow:0 2px 10px rgba(0,0,0,0.2);}
.pricing-banner p{color:rgba(255,255,255,0.95);font-size:1.3rem;max-width:800px;margin:0 auto;line-height:1.6;font-weight:300;}
.banner-content{position:relative;z-index:2;}
.pricing-intro{text-align:center;max-width:900px;margin:0 auto 5rem;padding:0 1.5rem;}
.pricing-intro h2{color:#1e3c72;font-weight:700;margin-bottom:1.5rem;font-size:2.2rem;position:relative;display:inline-block;}
.pricing-intro h2::after{content:"";position:absolute;width:80px;height:4px;background:linear-gradient(90deg,#1e3c72,#4f8cff);bottom:-12px;left:50%;transform:translateX(-50%);border-radius:2px;}
.pricing-intro p{color:#4b5563;font-size:1.15rem;line-height:1.7;margin-top:2rem;}
.pricing-card-wrapper{perspective:1000px;margin-bottom:2.5rem;}
.pricing-card{border-radius:16px;overflow:hidden;border:none;transition:all 0.5s cubic-bezier(0.23,1,0.32,1);background:white;box-shadow:0 10px 40px rgba(0,0,0,0.05);transform-style:preserve-3d;}
.pricing-card-header {
    background: url('/images/flag.jpg') center center/cover no-repeat, #2a3b4d;
    color: #fff;
    padding: 1.2rem 1rem 1rem 1rem;
    font-size: 1.15rem;
    font-weight: 700;
    text-align: left;
    position: relative;
    border-bottom: 2px solid #e5e7eb;
}
.pricing-card.premium .pricing-card-header {
    background: url('/images/flag.jpg') center center/cover no-repeat, #d32f2f;
}
.pricing-card-header .popular-badge {
    position: absolute;
    top: 0.7rem;
    right: 1rem;
    background: #d32f2f;
    color: #fff;
    font-size: 0.85rem;
    font-weight: 700;
    padding: 0.3rem 0.8not hisrem;
    border-radius: 8px;
    letter-spacing: 1px;
}
.pricing-card .price {
    font-size: 2.2rem;
    font-weight: 800;
    color: #d32f2f;
    margin: 1.2rem 0 0.7rem 0;
}
.pricing-card.premium .price {
    color: #fff;
    background: #d32f2f;
    padding: 0.5rem 0;
    border-radius: 8px;
}
.pricing-card .desc {
    font-size: 1rem;
    color: #444;
    margin-bottom: 1rem;
}
.pricing-card.premium .desc {
    color: #fff;
}
.pricing-card .features {
    list-style: none;
    padding: 0;
    margin-bottom: 1.2rem;
}
.pricing-card .features li {
    display: flex;
    align-items: center;
    font-size: 0.98rem;
    color: #444;
    margin-bottom: 0.5rem;
}
.pricing-card.premium .features li {
    color: #fff;
}
.pricing-card .features li .check {
    color: #2a3b4d;
    margin-right: 0.7rem;
    font-size: 1.1rem;
}
.pricing-card.premium .features li .check {
    color: #fff;
}
.pricing-card .sign-up-btn {
    display: block;
    width: 100%;
    background: #d32f2f;
    color: #fff;
    font-weight: 700;
    border: none;
    border-radius: 8px;
    padding: 0.9rem 0;
    font-size: 1.1rem;
    margin-top: 1.2rem;
    transition: background 0.2s;
    text-align: center;
}
.card-premium .sign-up-btn {
    background: #1e3c72;
}
.card-premium .sign-up-btn:hover {
    background: #14234a;
}
.pricing-card:hover{transform:translateY(-15px) rotateY(2deg);box-shadow:0 20px 40px rgba(0,0,0,0.1);}
.pricing-card .card-body{padding:2.5rem;display:flex;flex-direction:column;height:100%;}
.pricing-card-header{margin-bottom:2rem;}
.pricing-badge{display:inline-block;padding:0.5rem 1rem;border-radius:30px;font-weight:600;font-size:0.8rem;letter-spacing:1px;margin-bottom:1.2rem;}
.badge-basic{background:linear-gradient(135deg,#e0f2fe,#bae6fd);color:#0369a1;}
.badge-popular{background:linear-gradient(135deg,#fef3c7,#fde68a);color:#92400e;}
.badge-premium{background:linear-gradient(135deg,#dbeafe,#bfdbfe);color:#1e40af;}
.pricing-card .card-title{font-size:1.8rem;font-weight:700;margin-bottom:0.8rem;color:#1e3c72;}
.pricing-tag{font-size:2.5rem;font-weight:800;color:#4f8cff;margin-bottom:0.5rem;display:block;}
.pricing-period{font-size:0.9rem;color:#6b7280;display:block;margin-bottom:1.5rem;}
.pricing-description{color:#4b5563;font-size:1rem;line-height:1.6;margin-bottom:2rem;flex-grow:0;}
.pricing-features{list-style:none;padding:0;margin:0 0 2rem;flex-grow:1;}
.pricing-features li{padding:0.6rem 0;display:flex;align-items:center;color:#4b5563;}
.pricing-features li::before{content:"";display:inline-block;width:24px;height:24px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234f8cff'%3E%3Cpath d='M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z'/%3E%3C/svg%3E");background-size:contain;margin-right:12px;flex-shrink:0;}
.btn-pricing{padding:1rem 2rem;font-weight:600;font-size:1rem;border-radius:12px;border:none;color:white;transition:all 0.3s ease;position:relative;overflow:hidden;z-index:1;}
.btn-pricing::before{content:'';position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(45deg,#1e3c72,#4f8cff);transition:all 0.5s ease;z-index:-1;}
.btn-pricing:hover::before{background:linear-gradient(45deg,#4f8cff,#1e3c72);}
.btn-pricing:hover{transform:translateY(-3px);box-shadow:0 10px 20px rgba(79,140,255,0.3);color:white;}
.popular-card{position:relative;transform:scale(1.05);z-index:2;border:2px solid transparent;background:linear-gradient(white,white) padding-box,linear-gradient(135deg,#4f8cff,#1e3c72) border-box;box-shadow:0 15px 50px rgba(79,140,255,0.15);}
.popular-card:hover{transform:translateY(-15px) scale(1.05);}
.popular-card .pricing-tag{color:#1e3c72;}
.pricing-disclaimer{text-align:center;margin-top:4rem;padding:2rem 1rem;color:#6b7280;font-size:0.95rem;max-width:900px;margin-left:auto;margin-right:auto;border-top:1px solid #e5e7eb;}
.pricing-disclaimer p{margin-bottom:1rem;}
.back-home-btn{display:inline-block;padding:0.8rem 2rem;margin-top:1.5rem;font-weight:600;border-radius:12px;color:#1e3c72;background:transparent;border:2px solid #1e3c72;transition:all 0.3s ease;}
.back-home-btn:hover{background:#1e3c72;color:white;transform:translateY(-3px);box-shadow:0 5px 15px rgba(30,60,114,0.2);}
@media (max-width:991px){.pricing-banner h1{font-size:2.8rem;}.popular-card{transform:scale(1);}.popular-card:hover{transform:translateY(-15px) scale(1);}}
@media (max-width:767px){.pricing-banner{padding:4rem 0;border-radius:0 0 15% 15% / 5%;}.pricing-banner h1{font-size:2.2rem;}.pricing-banner p{font-size:1.1rem;}.pricing-intro h2{font-size:1.8rem;}}
</style>

<div class="pricing-banner text-center d-flex align-items-center justify-content-center">
    <img src="/images/pricing_banner.webp" alt="Pricing Banner" class="banner-photo-bg" />
    <div class="container banner-content">
        <div style="max-width:800px;margin:0 auto;">
            <div style="font-size:1.3rem;color:#e0eafc;letter-spacing:1px;font-weight:500;margin-bottom:1.2rem;text-align:left;">PACKAGES & PRICING</div>
            <h1 style="font-size:3.2rem;font-weight:800;line-height:1.1;color:#fff;text-align:left;margin-bottom:1.2rem;">Simplify Your Immigration Journey<br>with Tailored Packages</h1>
            <p style="font-size:1.25rem;color:#e0eafc;font-weight:400;text-align:left;line-height:1.5;">Explore our flexible immigration service packages designed to meet your unique needs. Whether you’re applying for a Green Card, fiancé visa, or naturalization, we offer transparent pricing and expert guidance every step of the way.</p>
        </div>
    </div>
</div>

<div class="container">
    <div class="pricing-intro">
        <h2>Explore Our Comprehensive Immigration Packages</h2>
        @if($visaType)
            <p style="font-weight:500;color:#374151;">Showing packages tailored to your <strong>{{ $visaType }}</strong> case.</p>
        @else
            <p>We offer tailored immigration service packages, each designed to fit different needs. Browse through the options below to find the perfect solution for your immigration journey.</p>
        @endif
        @if($visaType)
            <div style="margin-top:1rem;font-size:0.95rem;color:#1e3c72;font-weight:600;">Filtered for visa type: <span class="badge bg-primary" style="background:#4f8cff;">{{ $visaType }}</span>@if(!empty($sourceTerminal)) <span style="margin-left:.5rem;font-weight:400;color:#374151;">(terminal: {{ $sourceTerminal }})</span>@endif <a href="/pricing" style="margin-left:0.75rem;font-weight:500;">Clear</a></div>
        @endif
    </div>

    @if(isset($globalPackages) && $globalPackages->count())
        <div class="row justify-content-center mb-4">
            <div class="col-12 mb-3">
                <h3 style="color:#1e3c72;font-weight:700;margin-bottom:1.5rem;padding:2rem 0;">General Service Packages @if($visaType) <small style="font-size:60%;color:#6b7280;">(matching your quiz result)</small>@endif</h3>
            </div>
            @foreach($globalPackages as $package)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="pricing-card card-{{ strtolower($package->name) }} {{ strtolower($package->name) == 'premium' ? 'premium' : '' }}">
                        <div class="pricing-card-level" style="background:{{ strtolower($package->name) == 'premium' ? '#d32f2f' : (strtolower($package->name) == 'advanced' ? '#4e73df' : '#2a3b4d') }};color:#fff;padding:0.8rem 1rem;font-size:1.25rem;font-weight:700;text-align:left;border-top-left-radius:16px;border-top-right-radius:16px;position:relative;">
                            {{ strtoupper($package->name) }}
                            @if(strtolower($package->name) == 'advanced')
                                <span style="position:absolute;top:25px;right:-45px;width:180px;height:32px;background:#d32f2f;color:#fff;font-size:1.05rem;font-weight:700;display:flex;align-items:center;justify-content:center;transform:rotate(35deg);box-shadow:0 2px 8px rgba(0,0,0,0.12);letter-spacing:1px;z-index:10;">POPULAR</span>
                            @endif
                        </div>
                        <div class="pricing-card-header" style="background: linear-gradient(120deg, rgba(30,60,114,0.85) 0%, rgba(30,60,114,0.85) 100%), url('/images/flag.jpg') center center/cover no-repeat, #f8fafc; color:#fff; padding:2.4rem 1rem; font-size:1.05rem; font-weight:600; text-align:center; border-bottom:1px solid #e5e7eb;">
                            @if($visaType) {{ $visaType }} @else Immigration Support @endif
                        </div>
                        <div class="card-body" style="padding-top:0;padding-bottom:0.2rem;display:flex;flex-direction:column;align-items:center;{{ strtolower($package->name) == 'premium' ? 'background:#d32f2f;' : '' }}">
                            <div class="price" style="margin-top:0;margin-bottom:0.5rem;align-self:flex-start;">${{ number_format($package->price, 2) }}</div>
                            <ul class="features" style="margin-top:1rem;">
                                @foreach($package->features as $feature)
                                    <li><span class="check">&#10003;</span>{{ $feature }}</li>
                                @endforeach
                            </ul>
                            <a href="/register?pkg={{ $package->id }}@if($visaType)&vt={{ urlencode($visaType) }}@elseif($package->visa_type)&vt={{ urlencode($package->visa_type) }}@endif" class="sign-up-btn" style="margin-top:1.2rem;">Sign up</a>
                            <div style="height: 1.5rem;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    <div class="row justify-content-center">
    @foreach($visaCategories as $category)
        @if($category->packages->count())
            <div class="col-12 mb-5">
                <h3 style="color:#1e3c72;font-weight:700;margin-bottom:1.5rem;padding:2rem 0;">{{ $category->name }}</h3>
                <div class="row">
                    @foreach($category->packages as $package)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="pricing-card card-{{ strtolower($package->name) }} {{ strtolower($package->name) == 'premium' ? 'premium' : '' }}">
                                <!-- Top header for package level -->
                                <div class="pricing-card-level" style="background:{{ strtolower($package->name) == 'premium' ? '#d32f2f' : (strtolower($package->name) == 'advanced' ? '#4e73df' : '#2a3b4d') }};color:#fff;padding:0.8rem 1rem 0.8rem 1rem;font-size:1.25rem;font-weight:700;text-align:left;border-top-left-radius:16px;border-top-right-radius:16px;position:relative;">
                                        {{ strtoupper($package->name) }}
                                        @if(strtolower($package->name) == 'advanced')
                                            <span style="position:absolute;top:25px;right:-45px;width:180px;height:32px;background:#d32f2f;color:#fff;font-size:1.05rem;font-weight:700;display:flex;align-items:center;justify-content:center;transform:rotate(35deg);box-shadow:0 2px 8px rgba(0,0,0,0.12);letter-spacing:1px;z-index:10;">POPULAR</span>
                                        @endif
                                </div>
                                <!-- Visa type header -->
                                <div class="pricing-card-header" style="background: linear-gradient(120deg, rgba(30,60,114,0.85) 0%, rgba(30,60,114,0.85) 100%), url('/images/flag.jpg') center center/cover no-repeat, #f8fafc; color:#fff; padding:2.4rem 1rem 2.4rem 1rem; font-size:1.05rem; font-weight:600; text-align:center; border-bottom:1px solid #e5e7eb;">
                                    {{ $category->name }}
                                </div>
                                    <!-- Card body -->
                                    <div class="card-body" style="padding-top:0;padding-bottom:0.2rem;display:flex;flex-direction:column;align-items:center;{{ strtolower($package->name) == 'premium' ? 'background:#d32f2f;' : '' }}">
                                        <div class="price" style="margin-top:0;margin-bottom:0.5rem;align-self:flex-start;">${{ number_format($package->price, 2) }}</div>
                                    <!--<div class="desc" style="text-align:left;">{{ $package->description ?? 'Contact us for more details.' }}</div>-->
                                    <ul class="features" style="margin-top:1rem;">
                                        @foreach($package->features as $feature)
                                            <li><span class="check">&#10003;</span>{{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                    <a href="/register?pkg={{ $package->id }}@if($visaType)&vt={{ urlencode($visaType) }}@elseif($package->visa_type)&vt={{ urlencode($package->visa_type) }}@endif" class="sign-up-btn" style="margin-top:1.2rem;">Sign up</a>
                                    <div style="height: 1.5rem;"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        @endforeach
    </div>
    @if(isset($totalShown) && $visaType && $totalShown===0)
        <div style="text-align:center;padding:3rem 1rem;">
            <h4 style="color:#1e3c72;font-weight:700;">No packages available yet for {{ $visaType }}</h4>
            <p style="color:#4b5563;max-width:600px;margin:0.75rem auto 1.5rem;">We're preparing tailored packages for this visa category. Please check back soon or contact support.</p>
            <a href="/" class="btn btn-outline-primary">Back Home</a>
        </div>
    @endif
    
    <div class="pricing-disclaimer">
        <p>* Packages & pricing do not include required government fees. Government fees are paid directly to USCIS upon filing. *</p>
        <p>Horizon Pathways is a private document preparation service and is not a law firm, and is not affiliated with the U.S. Citizenship and Immigration Services (USCIS) or any government agency.</p>
        <div class="text-center mt-4">
            <a href="/" class="btn btn-outline-primary">Back to Home</a>
                </div>
            </div>
</div>
@endsection
