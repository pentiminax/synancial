<?php

namespace App\Service;

use App\Entity\Crowdlending;
use App\Repository\CrowdlendingRepository;
use Symfony\Bundle\SecurityBundle\Security;

class CrowdlendingService
{
    public function __construct(
        private readonly CrowdlendingRepository $crowdlendingRepo,
        private readonly Security $security
    ) {}

    public function add(Crowdlending $crowdlending): void
    {
        $crowdlending->setOwner($this->security->getUser());

        $this->crowdlendingRepo->add($crowdlending, true);
    }
}