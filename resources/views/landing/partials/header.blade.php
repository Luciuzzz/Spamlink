<header id="site-header" class="landing-header">
    <div class="landing-header__inner">
        @if(!empty($settings?->logo_path))
            <img
                src="{{ asset('storage/'.$settings->logo_path) }}"
                alt="{{ $settings->company_name ?? 'Empresa' }}"
                class="landing-header__logo">
        @else
            <span class="landing-header__title">{{ $settings?->company_name ?? 'Empresa' }}</span>
        @endif
    </div>
</header>
