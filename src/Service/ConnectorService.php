<?php

namespace App\Service;

use App\Repository\ConnectorRepository;

class ConnectorService
{
    public function __construct(
        private readonly ConnectorRepository $connectorRepo
    )
    {
    }

    public function findAllIndexedByProducts(): array
    {
        return $this->connectorRepo->findAllIndexedByProducts();
    }
}