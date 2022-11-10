<?php

namespace App\Service;

use App\Entity\AccountType;
use App\Model\PowensApi\BankAccount;
use App\Model\DataInterface;

class ApiService
{
    /**
     * @param BankAccount[] $bankAccounts
     */
    public function aggregateAssetsAccounts(DataInterface &$viewData, array $bankAccounts): void
    {
        $distribution = $viewData->getDistribution();

        $checkingAsset = $distribution->getChecking();
        $loanAsset = $distribution->getLoan();
        $marketAsset = $distribution->getMarket();
        $savingsAsset = $distribution->getSavings();

        foreach ($bankAccounts as $account) {
            $balance = $account->balance;
            switch ($account->type) {
                case AccountType::CHECKING:
                    $checkingAsset->addAmount($balance);
                    break;
                case AccountType::LOAN:
                    $loanAsset->addAmount(-$balance);
                    break;
                case AccountType::LIFEINSURANCE:
                case AccountType::MARKET:
                    $marketAsset->addAmount($balance);
                    break;
                case AccountType::SAVINGS:
                    $savingsAsset->addAmount($balance);
                    break;
                default:
                    break;
            }
        }

        $viewData->processCalculatedData();
    }
}