<?php

namespace App\Service;

use App\Entity\Symbol;
use App\Repository\SymbolRepository;

class SymbolService
{
    public function __construct(
        private readonly SymbolRepository $symbolRepo
    ) {

    }

    /**
     * @return Symbol[]
     */
    public function findAllWithLimit(int $limit): array
    {
        return $this->symbolRepo->findBy([], null, $limit);
    }
}