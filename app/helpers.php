<?php

use Illuminate\Support\HtmlString;

if (!function_exists('lucide_icon')) {
    function lucide_icon(string $name, string $classes = 'w-6 h-6')
    {
        $name = \Illuminate\Support\Str::of($name)->lower()->replace(' ', '-');
        return new HtmlString(
            "<svg class='{$classes}' data-lucide='{$name}'></svg>"
        );
    }
}
