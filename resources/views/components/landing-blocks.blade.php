@if(!empty($blocks))
<section class="mt-12 space-y-10 flex flex-col items-center">

    @foreach($blocks as $block)

        @switch($block['type'])

            {{-- BLOQUE DE TEXTO --}}
            @case('text')
                <div class="text-white/90 text-base leading-relaxed w-full max-w-[90vw] mx-auto text-center">
                    {!! nl2br(e($block['data']['content'] ?? '')) !!}
                </div>
            @break

            {{-- BLOQUE DE IMAGEN (SLIDER) --}}
@case('image')
    @php
        $images = $block['data']['images'] ?? [];
        $sliderId = 'slider_' . uniqid();
    @endphp

    @if(!empty($images))
        <div class="w-full flex flex-col items-center space-y-3">

            {{-- SLIDER --}}
            <div id="{{ $sliderId }}"
                 class="slider-container w-full max-w-xl relative overflow-hidden rounded-xl">

                @foreach($images as $index => $img)
                    <div class="slide {{ $index === 0 ? 'active' : '' }}">
                        <img
                            src="{{ asset('storage/' . $img) }}"
                            alt="Imagen"
                            class="mx-auto block"
                        >
                    </div>
                @endforeach
            </div>

            {{-- CONTROLES --}}
            <div class="flex items-center justify-center gap-4">

                {{-- Flecha izquierda --}}
                <button class="prev bg-white/20 hover:bg-white/40 text-white rounded-full px-4 py-2 text-2xl">
                    ‹
                </button>

                {{-- Dots --}}
                <div class="dots flex gap-2">
                    @foreach($images as $index => $img)
                        <div class="dot {{ $index === 0 ? 'active' : '' }}"></div>
                    @endforeach
                </div>

                {{-- Flecha derecha --}}
                <button class="next bg-white/20 hover:bg-white/40 text-white rounded-full px-4 py-2 text-2xl">
                    ›
                </button>
            </div>
        </div>

        {{-- JS --}}
        <script>
            (function () {
                const slider = document.getElementById('{{ $sliderId }}');
                const slides = slider.querySelectorAll('.slide');
                const prevBtn = slider.parentElement.querySelector('.prev');
                const nextBtn = slider.parentElement.querySelector('.next');
                const dots = slider.parentElement.querySelectorAll('.dot');

                let current = 0;
                let timer = null;
                const INTERVAL = 5000;

                function showSlide(index) {
                    slides.forEach((s, i) => {
                        s.classList.toggle('active', i === index);
                    });
                    dots.forEach((d, i) => {
                        d.classList.toggle('active', i === index);
                    });
                    current = index;
                }

                function next() {
                    showSlide((current + 1) % slides.length);
                }

                function prev() {
                    showSlide((current - 1 + slides.length) % slides.length);
                }

                function startTimer() {
                    timer = setInterval(next, INTERVAL);
                }

                function resetTimer() {
                    clearInterval(timer);
                    startTimer();
                }

                nextBtn.addEventListener('click', () => { next(); resetTimer(); });
                prevBtn.addEventListener('click', () => { prev(); resetTimer(); });

                dots.forEach((dot, i) => {
                    dot.addEventListener('click', () => {
                        showSlide(i);
                        resetTimer();
                    });
                });

                // Swipe móvil
                let startX = 0;
                slider.addEventListener('touchstart', e => {
                    startX = e.touches[0].clientX;
                });

                slider.addEventListener('touchend', e => {
                    const endX = e.changedTouches[0].clientX;
                    if (startX - endX > 40) next();
                    else if (endX - startX > 40) prev();
                    resetTimer();
                });

                startTimer();
            })();
        </script>
        {{-- CSS --}}
        <style>
            .slider-container {
                max-width: 100%;
            }
            .slide {
                opacity: 0;
                transition: opacity 0.6s ease;
                position: absolute;
                inset: 0;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .slide img {
                max-width: 100%;
                max-height: 60vh;
                width: auto;
                height: auto;
            }
            .slide.active {
                opacity: 1;
                position: relative;
            }
            .dot {
                width: 10px;
                height: 10px;
                border-radius: 9999px;
                background: rgba(255,255,255,0.5);
                cursor: pointer;
                transition: background 0.3s;
            }
            .dot.active {
                background: white;
            }
        </style>
    @endif
@break

            {{-- BLOQUE DE VIDEO --}}
@case('video')
    @php
        $data = $block['data'] ?? [];
        $source = $data['source'] ?? null;
        $url = null;

        if ($source === 'youtube' && !empty($data['url'])) {
            parse_str(parse_url($data['url'], PHP_URL_QUERY), $params);
            if (!empty($params['v'])) {
                $url = "https://www.youtube.com/embed/{$params['v']}?autoplay=1&mute=1&loop=1&playsinline=1";
            }
        }

        if ($source === 'vimeo' && !empty($data['url'])) {
            if (preg_match('/vimeo\.com\/(\d+)/', $data['url'], $m)) {
                $url = "https://player.vimeo.com/video/{$m[1]}?autoplay=1&muted=1&loop=1";
            }
        }
    @endphp

    <div class="w-full flex justify-center">
        @if($source === 'local' && !empty($data['file']))
            <video
                src="{{ asset('storage/'.$data['file']) }}"
                autoplay
                muted
                loop
                playsinline
                controls
                class="max-w-full max-h-[70vh] rounded-xl shadow-lg">
            </video>

        @elseif($url)
            <iframe
                src="{{ $url }}"
                class="aspect-video w-full max-w-4xl rounded-xl shadow-lg"
                allow="autoplay; encrypted-media"
                allowfullscreen>
            </iframe>
        @endif
    </div>
@break

        @endswitch

    @endforeach

</section>
@endif
