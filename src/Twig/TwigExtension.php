<?php

namespace App\Twig;

use App\Entity\User;
use App\Model\Investment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('diff_sign', [$this, 'formatDiffSign']),
            new TwigFilter('diff_class', [$this, 'formatDiffClass']),
            new TwigFilter('is_sync_button_disabled', [$this, 'isSyncButtonDisabled']),
            new TwigFilter('simplifyWording', [$this, 'simplifyOriginalWording']),
            new TwigFilter('currency_performance', [$this, 'performanceInCurrency']),
            new TwigFilter('percentage_performance', [$this, 'performanceInPercentage']),
        ];
    }

    public function formatDiffSign(float $number): string
    {
        if ($number > 0) {
            return "+$number";
        }

        return $number;
    }

    public function formatDiffClass(float $number): string
    {
        if ($number > 0) {
            return 'text-success';
        }

        return 'text-danger';
    }

    public function isSyncButtonDisabled(User $user): bool
    {
        $lastSync = $user->getLastSync();

        if (null === $lastSync) {
            return false;
        }

        $now = (new \DateTime());

        return $lastSync->diff($now)->h < 1 && $lastSync->diff($now)->d < 1;
    }

    public function simplifyOriginalWording(string $originalWording): string
    {
        $simplifiedWording = preg_replace("/(\d+|\d+[a-zA-Z])/", '', $originalWording);

        return str_replace('/', '', $simplifiedWording);
    }

    public function performanceInCurrency(Investment $investment): float
    {
        return round(($investment->unitvalue * $investment->quantity) - ($investment->unitprice * $investment->quantity), 2);
    }

    public function performanceInPercentage(Investment $investment): float
    {
        return round(($investment->unitvalue - $investment->unitprice) / ($investment->unitprice / 100), 2);
    }
}