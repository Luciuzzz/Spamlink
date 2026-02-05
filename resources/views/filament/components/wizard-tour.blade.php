@props([
    'steps' => [],
])

@if(!auth()->user()?->wizard_completed && !empty($steps))
    <div class="contents"
         x-data="wizardTour(@js($steps))"
         x-init="init()"
         x-cloak
    >
        <div
            x-ref="tooltip"
            x-show="open"
            class="fixed z-50 max-w-sm rounded-lg border border-primary-200 bg-white p-4 text-sm text-gray-700 shadow-lg"
            :style="style"
        >
            <div class="mb-2 font-semibold text-primary-700" x-text="current.title"></div>
            <div class="mb-3" x-text="current.body"></div>

            <div class="flex items-center justify-between gap-2">
                <button
                    type="button"
                    class="rounded-md border border-gray-300 px-2 py-1 text-xs font-semibold text-gray-600 hover:bg-gray-50"
                    @click="prev()"
                    :disabled="index === 0"
                    :class="index === 0 ? 'opacity-50 cursor-not-allowed' : ''"
                >
                    Anterior
                </button>

                <button
                    type="button"
                    class="rounded-md bg-primary-600 px-3 py-1 text-xs font-semibold text-white hover:bg-primary-700"
                    @click="next()"
                    x-text="index === steps.length - 1 ? 'Entendido' : 'Siguiente'"
                ></button>
            </div>
        </div>

        <style>
            .wizard-tour-highlight {
                outline: 2px solid rgb(59 130 246 / 0.9);
                outline-offset: 4px;
                border-radius: 8px;
            }
        </style>
    </div>

    <script>
        window.wizardTour = window.wizardTour || function (steps) {
            return {
                steps,
                index: 0,
                open: true,
                style: '',
                current: steps[0] || {},
                currentTarget: null,
                init() {
                    this.$nextTick(() => {
                        this.update();
                    });
                    window.addEventListener('resize', () => this.update());
                    window.addEventListener('scroll', () => this.update(), { passive: true });
                },
                update() {
                    if (!this.open || !this.steps.length) {
                        this.clearHighlight();
                        return;
                    }

                    this.current = this.steps[this.index] || {};
                    const target = document.querySelector(this.current.selector);

                    if (!target) {
                        if (this.index < this.steps.length - 1) {
                            this.index += 1;
                            this.update();
                        }
                        return;
                    }

                    this.setHighlight(target);
                    target.scrollIntoView({ behavior: 'smooth', block: 'center' });

                    const rect = target.getBoundingClientRect();
                    const tooltip = this.$refs.tooltip;
                    if (!tooltip) return;

                    const tooltipRect = tooltip.getBoundingClientRect();
                    const padding = 12;
                    let top = rect.bottom + padding;
                    let left = rect.left + rect.width / 2;

                    if (top + tooltipRect.height > window.innerHeight - padding) {
                        top = rect.top - tooltipRect.height - padding;
                    }

                    if (top < padding) {
                        top = padding;
                    }

                    if (left < padding) left = padding;
                    if (left > window.innerWidth - padding) left = window.innerWidth - padding;

                    const maxLeft = window.innerWidth - tooltipRect.width - padding;
                    const clampedLeft = Math.min(Math.max(left - tooltipRect.width / 2, padding), maxLeft);
                    this.style = `top: ${top}px; left: ${clampedLeft}px;`;
                },
                next() {
                    if (this.index < this.steps.length - 1) {
                        this.index += 1;
                        this.update();
                    } else {
                        this.open = false;
                        this.clearHighlight();
                    }
                },
                prev() {
                    if (this.index > 0) {
                        this.index -= 1;
                        this.update();
                    }
                },
                setHighlight(target) {
                    if (this.currentTarget && this.currentTarget !== target) {
                        this.currentTarget.classList.remove('wizard-tour-highlight');
                    }
                    this.currentTarget = target;
                    this.currentTarget.classList.add('wizard-tour-highlight');
                },
                clearHighlight() {
                    if (this.currentTarget) {
                        this.currentTarget.classList.remove('wizard-tour-highlight');
                    }
                }
            };
        };
    </script>
@endif
