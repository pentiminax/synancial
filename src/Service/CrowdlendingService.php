<?php

namespace App\Service;

use App\Entity\Crowdlending;
use App\Entity\User;
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

    public function getCrowdlendingsIndexedByPlatform(User $user): array
    {
        $crowdlendingsIndexedByPlatform = [];

        $user->getCrowdlendings()->map(function (Crowdlending $crowdlending) use (&$crowdlendingsIndexedByPlatform, &$totalInvestedAmount) {
            $platform = $crowdlending->getPlatform();

            if (!isset($crowdlendingsIndexedByPlatform[$platform->getId()])) {
                $crowdlendingsIndexedByPlatform[$platform->getId()]['platformName'] = $platform->getName();
            }

            $crowdlendingsIndexedByPlatform[$platform->getId()]['crowdlendings'][] = [
                'id' => $crowdlending->getId(),
                'name' => $crowdlending->getName(),
                'investedAmount' => $crowdlending->getInvestedAmount(),
                'currentValue' => $crowdlending->getCurrentValue(),
                'duration' => $crowdlending->getDuration(),
                'investmentDate' => $crowdlending->getInvestmentDate(),
                'annualYield' => $crowdlending->getAnnualYield(),
            ];

        });

        return $crowdlendingsIndexedByPlatform;
    }

    public function getTotalInvestedAmount(User $user): float
    {
        return array_sum($user->getCrowdlendings()->map(fn (Crowdlending $crowdlending) => $crowdlending->getInvestedAmount())->toArray());
    }
}