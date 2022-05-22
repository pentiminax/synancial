<?php

namespace App\Service;

use App\Entity\TimeSerie;
use App\Entity\User;
use App\Repository\TimeSerieRepository;
use Symfony\Component\Security\Core\Security;

class TimeSerieService
{
    public function __construct(
        private readonly Security $security,
        private readonly TimeSerieRepository $timeSerieRepo
    ) {
    }

    public function add(int $accountId, float $value): void
    {
        /** @var User */
        $user = $this->security->getUser();

        $timeSerie = new TimeSerie($accountId, $value, new \DateTime(), $user);

        $userTimeSerieAlreadyInDb = $user->getTimeSeries()->filter(
            fn (TimeSerie $userTimeSerie) => $userTimeSerie->getIdAccount() === $timeSerie->getIdAccount() && $userTimeSerie->getDate()->diff($timeSerie->getDate())->d === 0);


        if ($userTimeSerieAlreadyInDb->isEmpty()) {
            $this->timeSerieRepo->add($timeSerie, true);
        }
    }
}