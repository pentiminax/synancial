<?php

namespace App\Service;

use App\Entity\Dividend;
use App\Entity\Symbol;
use App\Model\BankAccount;
use App\Model\Investment;
use App\Repository\DividendRepository;
use App\Repository\SymbolRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DividendService
{
    public function __construct(
        private readonly DividendRepository $dividendRepo,
        private readonly HttpClientInterface $divvydiaryClient,
        private SerializerInterface $serializer,
        private readonly SymbolRepository $symbolRepo,
    )
    {
    }

    /**
     * @param Investment[] $investments
     */
    public function getDividendsAmountByInvestments(array $investments): int
    {
        $dividendsAmount = 0;
        $investmentsData = [];

        foreach ($investments as $investment) {
            if (Investment::CODE_TYPE_ISIN !== $investment->code_type) {
                continue;
            }

            $isins[] = $investment->code;

            $investmentsData[$investment->code] = [
                'code' => $investment->code,
                'quantity' => $investment->quantity
            ];
        }

        $symbols = $this->symbolRepo->findAllThatPayDividendsByISINS($isins);
        $dividends = $this->dividendRepo->findAllBySymbolsForCurrentYear($symbols);

        foreach ($dividends as $dividend) {
            $isin = $dividend->getSymbol()->getIsin();
            if (!isset($investmentsData[$isin])) {
                continue;
            }

            $dividendsAmount += $dividend->getAmount() * $investmentsData[$isin]['quantity'];
        }

        return intval(round($dividendsAmount));
    }

    /**
     * @return Dividend[]|null
     */
    public function findAllByISIN(string $isin): ?array
    {
        $symbol = $this->symbolRepo->findOneThatPayDividendsByISIN($isin);

        if (!$symbol) {
            return null;
        }

        $dividends = $this->dividendRepo->findBy(['symbol' => $symbol]);

        if (!$dividends) {
            $this->loadDividendsForSymbol($symbol);
        }

        return $dividends;
    }

    private function loadDividendsForSymbol(Symbol $symbol): void
    {
        $data = json_decode($this->divvydiaryClient->request(Request::METHOD_GET, "/symbols/{$symbol->getIsin()}")->getContent(), true);

        /** @var Dividend[] $dividends */
        $dividends = $this->serializer->deserialize(json_encode($data['dividends']), 'App\Entity\Dividend[]', 'json');

        $now = new \DateTime();

        foreach ($dividends as $dividend) {
            if ($dividend->getExDate()->diff($now)->y >= 5) {
                continue;
            }
            $dividend->setSymbol($symbol);
            $this->dividendRepo->save($dividend, true);
        }
    }
}