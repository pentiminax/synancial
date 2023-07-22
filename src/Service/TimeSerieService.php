<?php

namespace App\Service;

use App\Entity\TimeSerie;
use App\Entity\User;
use App\Repository\TimeSerieRepository;
use Symfony\Bundle\SecurityBundle\Security;

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

    public function processBarChart(array $line, array &$bar, int &$max): void
    {
        for ($i = 0; $i < count($line); $i++) {
            if (isset($line[$i + 1])) {
                $bar[] = [
                    'date' => $line[$i]['date'],
                    'value' => -1 * abs($line[$i]['value'] - $line[$i + 1]['value'])
                ];
            } else if (isset($line[$i - 1])) {
                $bar[] = [
                    'date' => $line[$i]['date'],
                    'value' => $line[$i]['value'] - $line[$i - 1]['value']
                ];
            } else {
                $bar[] = [
                    'date' => $line[$i]['date'],
                    'value' => 0
                ];
            }
            if (abs($bar[$i]['value']) > $max) {
                $max = ceil(abs($bar[$i]['value']) / 100) * 100;
            }
        }
    }

    public function processLineChart(int $id, array &$line): void
    {
        $timeseries = $this->security->getUser()->getTimeSeries($id);

        foreach ($timeseries as $timeserie) {
            $line[] = [
                'date' => $timeserie->getDate()->format('d M'),
                'value' => $timeserie->getValue()
            ];
        }
    }
}