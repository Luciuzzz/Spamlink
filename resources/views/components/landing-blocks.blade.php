@props([
    'blocks' => [],
    'title' => null,
    'description' => null,
])

@if(!empty($blocks) || $title || $description)
@once
    <style>
        .rich-content .color { color: var(--color); }
        @media (prefers-color-scheme: dark) {
            .rich-content .color { color: var(--dark-color); }
        }
        .rich-content mark.color { background-color: var(--color); }
        @media (prefers-color-scheme: dark) {
            .rich-content mark.color { background-color: var(--dark-color); }
        }
        .rich-content {
            line-height: 1.7;
            font-size: 16px;
        }
        .rich-content p + p {
            margin-top: 0.9rem;
        }
        .rich-content h2,
        .rich-content h3 {
            font-weight: 700;
            line-height: 1.3;
            margin: 1.1rem 0 0.6rem;
        }
        .rich-content h2 { font-size: 1.4rem; }
        .rich-content h3 { font-size: 1.2rem; }
        .rich-content a {
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        .rich-content ul,
        .rich-content ol {
            margin: 0.8rem 0 0.8rem 1.2rem;
            padding-left: 1rem;
        }
        .rich-content li {
            margin: 0.3rem 0;
        }
        .rich-content blockquote {
            border-left: 3px solid rgba(255, 255, 255, 0.5);
            padding: 0.6rem 0 0.6rem 0.9rem;
            margin: 0.9rem 0;
            opacity: 0.95;
            font-style: italic;
        }
        .rich-content pre {
            background: rgba(15, 23, 42, 0.6);
            color: #e2e8f0;
            padding: 0.8rem 1rem;
            border-radius: 10px;
            overflow-x: auto;
            margin: 0.9rem 0;
            font-size: 0.95rem;
        }
        .rich-content code {
            background: rgba(15, 23, 42, 0.4);
            color: #e2e8f0;
            padding: 0.1rem 0.35rem;
            border-radius: 6px;
            font-size: 0.95em;
        }
        .rich-content pre code {
            background: transparent;
            padding: 0;
        }
    </style>
@endonce
<section class="mt-12 space-y-10 flex flex-col items-center">
    @if($title || $description)
        <div class="text-center px-4">
            @if($title)
                <h1 class="text-3xl font-bold text-white">{{ $title }}</h1>
            @endif
            @if($description)
                <p class="mt-2 text-white/80">{{ $description }}</p>
            @endif
        </div>
    @endif

    @foreach($blocks as $block)
        @php
            if (! is_array($block)) {
                continue;
            }

            $blockData = $block['data'] ?? [];
            if (($blockData['is_active'] ?? true) === false) {
                continue;
            }
        @endphp

        @switch($block['type'])

            {{-- BLOQUE DE TEXTO --}}
            @case('text')
                @php
                    $data = $blockData;
                    $rawContent = $data['content'] ?? '';
                    $color = $data['text_color'] ?? '#ffffff';
                    $bg = $data['background_color'] ?? 'transparent';

                    if (is_array($rawContent)) {
                        $content = \Filament\Forms\Components\RichEditor\RichContentRenderer::make($rawContent)
                            ->fileAttachmentsDisk('public')
                            ->fileAttachmentsVisibility('public')
                            ->textColors([
                                ...\Filament\Forms\Components\RichEditor\TextColor::getDefaults(),
                            ])
                            ->toHtml();
                    } else {
                        $content = $rawContent;
                    }
                @endphp

                <div class="rich-content w-full max-w-[90vw] mx-auto p-4 rounded-xl"
                    style="color: {{ $color }}; background-color: {{ $bg }};">
                    {!! $content !!}
                </div>
            @break

            {{-- BLOQUE DE IMAGEN (SLIDER) --}}
            @case('image')
                @php
                    $images = $blockData['images'] ?? [];
                    $sliderId = 'slider_' . uniqid();
                @endphp

                @if(!empty($images))
                    <div class="w-full flex flex-col items-center space-y-3">

                        {{-- SLIDER --}}
                        <div id="{{ $sliderId }}"
                            class="slider-container w-full max-w-xl relative overflow-hidden rounded-xl">

                            @foreach($images as $index => $img)
                                @php
                                    $raw = $img;
                                    $path = null;
                                    $imageUrl = null;

                                    if (is_array($raw)) {
                                        $path = $raw['path'] ?? $raw['file'] ?? $raw['url'] ?? null;
                                    } else {
                                        $path = $raw;
                                    }

                                    if (is_string($path) && $path !== '') {
                                        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                                            $imageUrl = $path;
                                        } elseif (str_starts_with($path, '/storage/')) {
                                            $imageUrl = $path;
                                        } elseif (str_starts_with($path, 'storage/')) {
                                            $imageUrl = '/' . $path;
                                        } elseif (str_starts_with($path, 'public/')) {
                                            $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')
                                                ->url(ltrim(substr($path, 7), '/'));
                                        } else {
                                            $imageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
                                        }
                                    }
                                @endphp

                                @if($imageUrl)
                                <div class="slide {{ $index === 0 ? 'active' : '' }}">
                                    <img
                                        src="{{ $imageUrl }}"
                                        alt="Imagen"
                                        class="mx-auto block rounded-xl"
                                    >
                                </div>
                                @endif
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
                    $url = $blockData['embed_url'] ?? null;
                    $videoId = null;

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
