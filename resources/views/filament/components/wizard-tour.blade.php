@props([
    'steps' => [],
])

@php
    $filamentUser = \Filament\Facades\Filament::auth()->user();
@endphp

@if (!($filamentUser?->wizard_completed) && !empty($steps))
    <div
        class="contents"
        x-data="{
            steps: @js($steps),
            index: 0,
            open: false,
            style: '',
            arrowStyle: '',
            current: {},
            currentTarget: null,
            retryCount: 0,
            maxRetries: 30,
            retryDelay: 200,
            observer: null,
            init() {
                this.current = this.steps[0] || {};
                this.$nextTick(() => this.focusStep());
                this.handleViewportChange = () => this.updatePosition();
                window.addEventListener('resize', this.handleViewportChange);
                window.addEventListener('scroll', this.handleViewportChange, { passive: true });
                this.startObserver();

                if (window.Livewire?.hook) {
                    window.Livewire.hook('message.processed', () => {
                        this.focusStep();
                    });
                }
            },
            startObserver() {
                if (this.observer) return;
                this.observer = new MutationObserver(() => {
                    if (!this.currentTarget) {
                        this.focusStep();
                    }
                });
                this.observer.observe(document.body, { childList: true, subtree: true });
            },
            stopObserver() {
                if (!this.observer) return;
                this.observer.disconnect();
                this.observer = null;
            },
            scheduleFocus() {
                if (this.retryCount >= this.maxRetries) return;
                this.retryCount += 1;
                setTimeout(() => this.focusStep(), this.retryDelay);
            },
            focusStep() {
                if (!this.steps.length) return;
                this.current = this.steps[this.index] || {};
                const target = document.querySelector(this.current.selector);

                if (!target) {
                    this.scheduleFocus();
                    return;
                }

                this.setHighlight(target);
                this.retryCount = 0;
                this.open = true;
                this.stopObserver();
                target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                this.$nextTick(() => {
                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => this.updatePosition());
                    });
                });
            },
            updatePosition() {
                if (!this.open || !this.currentTarget) return;

                const rect = this.currentTarget.getBoundingClientRect();
                const tooltip = this.$refs.tooltip;
                if (!tooltip) return;

                const tooltipRect = tooltip.getBoundingClientRect();
                const padding = 12;
                const gap = 10;
                let placement = 'bottom';
                let top = rect.bottom + gap;

                if (top + tooltipRect.height > window.innerHeight - padding) {
                    placement = 'top';
                    top = rect.top - tooltipRect.height - gap;
                }

                if (top < padding) {
                    placement = 'bottom';
                    top = rect.bottom + gap;
                }

                const maxTop = window.innerHeight - tooltipRect.height - padding;
                top = Math.min(Math.max(top, padding), Math.max(padding, maxTop));

                let left = rect.left + rect.width / 2 - tooltipRect.width / 2;
                const maxLeft = window.innerWidth - tooltipRect.width - padding;
                left = Math.min(Math.max(left, padding), maxLeft);

                const centerX = rect.left + rect.width / 2;
                let arrowX = centerX - left - 6;
                arrowX = Math.min(Math.max(arrowX, 8), tooltipRect.width - 14);

                this.arrowStyle = placement === 'bottom'
                    ? `top: -6px; left: ${arrowX}px;`
                    : `bottom: -6px; left: ${arrowX}px;`;

                this.style = `top: ${top}px; left: ${left}px;`;
            },
            next() {
                if (this.index < this.steps.length - 1) {
                    this.index += 1;
                    this.focusStep();
                } else {
                    this.open = false;
                    this.clearHighlight();
                    if (this.current?.scrollTopOnClose) {
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    }
                }
            },
            prev() {
                if (this.index > 0) {
                    this.index -= 1;
                    this.focusStep();
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
        }"
        x-init="init()"
        x-cloak
    >
        <template x-teleport="body">
            <div
                x-ref="tooltip"
                x-show="open"
                class="wizard-tour-tooltip"
                :style="style"
            >
                <div
                    class="wizard-tour-arrow"
                    :style="arrowStyle"
                ></div>
                <div class="wizard-tour-title" x-text="current.title"></div>
                <div class="wizard-tour-body" x-text="current.body"></div>

                <div class="wizard-tour-actions">
                    <button
                        type="button"
                        class="wizard-tour-btn wizard-tour-btn-secondary"
                        @click="prev()"
                        :disabled="index === 0"
                        :class="index === 0 ? 'is-disabled' : ''"
                    >
                        Anterior
                    </button>

                    <button
                        type="button"
                        class="wizard-tour-btn wizard-tour-btn-primary"
                        @click="next()"
                        x-text="index === steps.length - 1 ? 'Entendido' : 'Siguiente'"
                    ></button>
                </div>
            </div>
        </template>

        <style>
            .wizard-tour-tooltip {
                position: fixed;
                z-index: 50;
                max-width: 360px;
                max-height: calc(100vh - 24px);
                overflow: auto;
                background: #ffffff;
                border: 1px solid #cbd5f5;
                border-radius: 12px;
                padding: 14px 14px 12px;
                color: #374151;
                font-size: 14px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.18);
            }

            .wizard-tour-arrow {
                position: absolute;
                width: 12px;
                height: 12px;
                background: #ffffff;
                border: 1px solid #cbd5f5;
                transform: rotate(45deg);
            }

            .wizard-tour-title {
                margin-bottom: 6px;
                font-weight: 600;
                color: #1e3a8a;
            }

            .wizard-tour-body {
                margin-bottom: 10px;
            }

            .wizard-tour-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
            }

            .wizard-tour-btn {
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                padding: 6px 10px;
                border: 1px solid transparent;
                cursor: pointer;
            }

            .wizard-tour-btn.is-disabled {
                opacity: 0.5;
                cursor: not-allowed;
            }

            .wizard-tour-btn-primary {
                background: #2563eb;
                color: #ffffff;
            }

            .wizard-tour-btn-secondary {
                background: #ffffff;
                color: #4b5563;
                border-color: #d1d5db;
            }

            .wizard-tour-highlight {
                outline: 2px solid rgba(59, 130, 246, 0.9);
                outline-offset: 4px;
                border-radius: 8px;
            }
        </style>
    </div>
@endif
