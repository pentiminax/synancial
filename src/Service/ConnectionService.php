<?php

namespace App\Service;

use App\Entity\Connection;
use App\Entity\Connector;
use App\Repository\ConnectionRepository;
use Symfony\Component\Security\Core\Security;

class ConnectionService
{
    public function __construct(
        private readonly ConnectionRepository   $connectionRepo,
        private readonly Security $security
    )
    {
    }

    public function add(Connector $connector, int $connectionId): void
    {
        $connection = new Connection();
        $connection->setConnector($connector);
        $connection->setId($connectionId);
        $connection->setLastUpdate(new \DateTime());
        $connection->setUser($this->security->getUser());

        $this->connectionRepo->add($connection, true);
    }
}