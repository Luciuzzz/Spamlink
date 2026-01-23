@if(!empty($blocks))
<section class="mt-12 space-y-10 flex flex-col items-center">

    @foreach($blocks as $block)

        @switch($block['type'])

            {{-- BLOQUE DE TEXTO --}}
            @case('text')
                @php
                    $data = $block['data'] ?? [];
                    $content = $data['content'] ?? '';
                    $color = $data['text_color'] ?? '#ffffff';
                    $bg = $data['background_color'] ?? 'transparent';
                @endphp

                <div class="w-full max-w-[90vw] mx-auto p-4 rounded-xl"
                    style="color: {{ $color }}; background-color: {{ $bg }};">
                    {!! $content !!}
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
                                        class="mx-auto block rounded-xl"
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
                            border-radius: 1rem;
                        }
                        .slide {
                            opacity: 0;
                            transition: opacity 0.6s ease;
                            position: absolute;
                            inset: 0;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            border-radius: 1rem;
                            overflow: hidden;
                        }
                        .slide img {
                            max-width: 100%;
                            max-height: 60vh;
                            width: auto;
                            height: auto;
                            border-radius: 1rem;
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
                    $url = $block['data']['embed_url'] ?? null;

                    if ($url) {
                        // YouTube normal
                        if (str_contains($url, 'youtube.com/watch?v=')) {
                            parse_str(parse_url($url, PHP_URL_QUERY), $params);
                            $videoId = $params['v'] ?? null;
                            $url = $videoId
                                ? "https://www.youtube.com/embed/$videoId"
                                : null;
                        }
                        // YouTube corto
                        elseif (str_contains($url, 'youtu.be/')) {
                            $videoId = trim(parse_url($url, PHP_URL_PATH), '/');
                            $url = "https://www.youtube.com/embed/$videoId";
                        }

                        // Parámetros de autoplay (mute obligatorio) y loop
                        if ($url && !empty($videoId)) {
                            $url .= "?autoplay=1&mute=1&playsinline=1&controls=1&rel=0&modestbranding=1&loop=1&playlist=$videoId";
                        }
                    }
                @endphp

                @if($url)
                    <div class="aspect-video w-full max-w-xl mx-auto rounded-xl overflow-hidden">
                        <iframe
                            src="{{ $url }}"
                            class="w-full h-full"
                            allow="autoplay; encrypted-media; picture-in-picture"
                            allowfullscreen
                        ></iframe>
                    </div>
                @endif
            @break

        @endswitch

    @endforeach

</section>
@endif
