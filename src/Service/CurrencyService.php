<?php

namespace App\Service;

use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CurrencyService
{
    public function __construct(
        private readonly CurrencyRepository $currencyRepo,
        private readonly ParameterBagInterface $parameters
    ) {

    }

    public function getDefaultCurrency(): Currency
    {
        return $this->currencyRepo->findOneBy([
            'code' => strtoupper($this->parameters->get('default_currency_code'))
        ]);
    }
}