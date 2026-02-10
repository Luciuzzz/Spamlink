<footer id="landingFooter" class="landing-footer">
    <div class="landing-footer__inner">
        <div class="landing-footer__left">
            &copy; {{ date('Y') }} {{ $settings->company_name ?? 'Empresa' }}. Todos los derechos reservados.
        </div>

        <div class="landing-footer__center">
            <img src="{{ asset('icons/logo.png') }}" alt="Logo" class="landing-footer__logo">
            <span class="landing-footer__brand">SpamLink</span>
            @guest
                <a href="{{ route('register') }}" class="landing-footer__cta">
                    ¿Querés el tuyo?
                </a>
            @else
                <a href="{{ route('dashboard') }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="landing-footer__cta">
                    Ir al panel
                </a>
            @endguest
        </div>

        <div class="landing-footer__right"></div>
    </div>
</footer>
