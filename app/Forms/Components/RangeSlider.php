<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Field;

class RangeSlider extends Field
{
    protected string $view = 'forms.components.range-slider';

    protected float $min = 0.1;
    protected float $max = 1.0;
    protected float $step = 0.05;

    public function min(float $value): static
    {
        $this->min = $value;
        return $this;
    }

    public function max(float $value): static
    {
        $this->max = $value;
        return $this;
    }

    public function step(float $value): static
    {
        $this->step = $value;
        return $this;
    }

    public function getMin(): float
    {
        return $this->min;
    }

    public function getMax(): float
    {
        return $this->max;
    }

    public function getStep(): float
    {
        return $this->step;
    }
}
