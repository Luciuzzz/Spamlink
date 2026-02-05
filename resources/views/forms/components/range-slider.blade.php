<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{ value: @entangle($getStatePath()) }"
        class="space-y-2"
    >
        <div class="flex items-center gap-3">
            <input
                type="range"
                x-model.number="value"
                min="{{ $getMin() }}"
                max="{{ $getMax() }}"
                step="{{ $getStep() }}"
                class="w-full"
                @disabled($isDisabled())
            />

            <input
                type="number"
                x-model.number="value"
                min="{{ $getMin() }}"
                max="{{ $getMax() }}"
                step="{{ $getStep() }}"
                class="w-20 rounded border border-gray-300 bg-white px-2 py-1 text-sm text-gray-900"
                @disabled($isDisabled())
            />
        </div>
    </div>
</x-dynamic-component>
