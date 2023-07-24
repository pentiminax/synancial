<?php

namespace App\Service;

use App\Entity\AccountType;
use App\Entity\Crowdlending;
use App\Entity\User;
use App\Model\PowensApi\BankAccount;
use App\Model\DataInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ApiService
{
    /**
     * @param BankAccount[] $bankAccounts
     */
    public function aggregateAssetsAccounts(DataInterface &$viewData, array $bankAccounts): void
    {
        $distribution = $viewData->getDistribution();

        foreach ($bankAccounts as $account) {
            $balance = $account->balance;
            switch ($account->type) {
                case AccountType::CHECKING:
                    $distribution->getChecking()->addAmount($balance);
                    break;
                case AccountType::REAL_ESTATE:
                    $distribution->getCrowdlendings()->addAmount($balance);
                    break;
                case AccountType::LOAN:
                    $distribution->getLoan()->addAmount(-$balance);
                    break;
                case AccountType::LIFEINSURANCE:
                case AccountType::MARKET:
                    $distribution->getMarket()->addAmount($balance);
                    break;
                case AccountType::SAVINGS:
                    $distribution->getSavings()->addAmount($balance);
                    break;
                default:
                    break;
            }
        }
    }

    public function aggregateCrowdlendingsAccounts(DataInterface &$viewData, User $user): void
    {
        $user->getCrowdlendings()->map(function (Crowdlending $crowdlending) use (&$viewData) {
            $viewData->getDistribution()->getCrowdlendings()->addAmount($crowdlending->getInvestedAmount());
        });
    }
}