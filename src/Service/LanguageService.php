<?php

namespace App\Service;

use App\Entity\Language;
use App\Repository\LanguageRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class LanguageService
{
    public function __construct(
        private readonly LanguageRepository $languageRepo,
        private readonly ParameterBagInterface $parameters
    ) {

    }

    public function getDefaultLanguage(): Language
    {
        return $this->languageRepo->findOneBy([
            'code' => strtoupper($this->parameters->get('kernel.default_locale'))
        ]);
    }
}