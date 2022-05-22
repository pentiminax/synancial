<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('diff_sign', [$this, 'formatDiffSign']),
            new TwigFilter('diff_class', [$this, 'fortmatDiffClass']),
        ];
    }

    public function formatDiffSign(float $number): string
    {
        if ($number > 0) {
            return "+$number";
        }

        return $number;
    }

    public function fortmatDiffClass(float $number): string
    {
        if ($number > 0) {
            return 'text-success';
        }

        return 'text-danger';
    }
}