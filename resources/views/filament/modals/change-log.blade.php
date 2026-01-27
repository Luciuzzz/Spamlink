<div style="display:flex;flex-direction:column;gap:1rem;font-family:system-ui,sans-serif;">

    {{-- Usuario --}}
    <div style="font-size:.875rem;color:#d1d5db;">
        <strong style="color:#ffffff;">Usuario:</strong>
        {{ $record->user?->name ?? '—' }}
    </div>

    {{-- Acción --}}
    <div style="font-size:.875rem;color:#d1d5db;">
        <strong style="color:#ffffff;">Acción:</strong>
        {{ $record->action }}
    </div>

    {{-- Fecha --}}
    <div style="font-size:.875rem;color:#d1d5db;">
        <strong style="color:#ffffff;">Fecha:</strong>
        {{ $record->created_at?->format('d/m/Y H:i') }}
    </div>

    {{-- Detalles --}}
    <div style="margin-top:1rem;">
        <strong style="display:block;margin-bottom:.5rem;color:#ffffff;">Detalles:</strong>

        @php
            $changes = $record->changes;

            if (is_string($changes)) {
                $decoded = json_decode($changes, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $changes = $decoded;
                }
            }
        @endphp

        @if(is_array($changes) && count($changes))
            <div style="display:flex;flex-direction:column;gap:.75rem;max-height:60vh;overflow:auto;">

                @foreach($changes as $value)
                    @php
                        $from = $value['from'] ?? null;
                        $to   = $value['to'] ?? null;

                        // Decodificar si vienen como string JSON
                        if (is_string($from)) {
                            $decodedFrom = json_decode($from, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $from = $decodedFrom;
                            }
                        }

                        if (is_string($to)) {
                            $decodedTo = json_decode($to, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $to = $decodedTo;
                            }
                        }
                    @endphp

                    {{-- Caso multimedia con bloques --}}
                    @if(is_array($from) && is_array($to) && isset($from['blocks'], $to['blocks']))
                        @php
                            $fromBlocks = $from['blocks'];
                            $toBlocks   = $to['blocks'];

                            $added = [];
                            $removed = [];
                            $modified = [];

                            foreach ($fromBlocks as $index => $block) {
                                if (!isset($toBlocks[$index])) {
                                    $removed[] = $block['type'] ?? 'desconocido';
                                    continue;
                                }
                                if (
                                    ($block['type'] ?? null) !== ($toBlocks[$index]['type'] ?? null) ||
                                    json_encode($block['data'] ?? []) !== json_encode($toBlocks[$index]['data'] ?? [])
                                ) {
                                    $modified[] = $block['type'] ?? 'desconocido';
                                }
                            }

                            foreach ($toBlocks as $index => $block) {
                                if (!isset($fromBlocks[$index])) {
                                    $added[] = $block['type'] ?? 'desconocido';
                                }
                            }
                        @endphp

                        <div style="display:flex;flex-direction:column;gap:.5rem;font-size:.875rem;">
                            @foreach($added as $type)
                                <div style="background:#dcfce7;color:#166534;padding:.5rem .75rem;border-radius:.5rem;">
                                    ➕ Bloque agregado: <strong>{{ $type }}</strong>
                                </div>
                            @endforeach

                            @foreach($removed as $type)
                                <div style="background:#fee2e2;color:#991b1b;padding:.5rem .75rem;border-radius:.5rem;">
                                    ➖ Bloque eliminado: <strong>{{ $type }}</strong>
                                </div>
                            @endforeach

                            @foreach($modified as $type)
                                <div style="background:#fef3c7;color:#92400e;padding:.5rem .75rem;border-radius:.5rem;">
                                    ✏️ Bloque modificado: <strong>{{ $type }}</strong>
                                </div>
                            @endforeach

                            @if(!count($added) && !count($removed) && !count($modified))
                                <div style="color:#9ca3af;">
                                    No se detectaron cambios estructurales en los bloques.
                                </div>
                            @endif
                        </div>

                    {{-- Caso simple (setting, string, número, etc.) --}}
                    @else
                        <div style="background:#f3f4f6;color:#374151;border-radius:.75rem;padding:.75rem;">
                            <strong>Valor cambiado:</strong>
                            <div><strong>From:</strong> {{ is_scalar($from) ? $from : json_encode($from, JSON_UNESCAPED_UNICODE) }}</div>
                            <div><strong>To:</strong> {{ is_scalar($to) ? $to : json_encode($to, JSON_UNESCAPED_UNICODE) }}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <p style="color:#9ca3af;font-size:.875rem;">
                No hay cambios registrados para este elemento.
            </p>
        @endif

    </div>
</div>
