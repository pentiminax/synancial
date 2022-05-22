<?php

namespace App\Service;

use App\Model\BankAccount;
use App\Model\ViewDataInterface;

class ApiService
{
    /**
     * @param BankAccount[] $bankAccounts
     */
    public function aggregateAssetsAccounts(ViewDataInterface &$viewData, array $bankAccounts): void
    {
        $distribution = $viewData->getDistribution();

        $checkingAsset = $distribution->getChecking();
        $loanAsset = $distribution->getLoan();
        $marketAsset = $distribution->getMarket();
        $savingsAsset = $distribution->getSavings();

        foreach ($bankAccounts as $account) {
            $balance = $account->balance;
            switch ($account->type) {
                case 'checking':
                    $checkingAsset->addAmount($balance);
                    break;
                case 'loan':
                    $loanAsset->addAmount(-$balance);
                    break;
                case 'market':
                    $marketAsset->addAmount($balance);
                    break;
                case 'savings':
                    $savingsAsset->addAmount($balance);
                    break;
                default:
                    break;
            }
        }

        $viewData->processCalculatedData();
    }

    /**
     * @param BankAccount[] $bankAccounts
     * @return array
     */
    public function aggregateLiabilitiesAccount(array $bankAccounts): array
    {
        $liabilities = [
            'loan' => [
                'amount' => 0,
                'share' => 0
            ],
        ];

        $totalLiabilitiesAmount = 0;

        foreach ($bankAccounts as $account) {
            switch ($account->type) {
                case 'loan':
                    $liabilities['loan']['amount'] += 0 - $account->balance;
                    $totalLiabilitiesAmount+= 0 - $account->balance;
                    break;
                default:
                    break;
            }
        }

        $liabilities['loan']['share'] = ($liabilities['loan']['amount'] / $totalLiabilitiesAmount) * 100;

        return $liabilities;
    }
}