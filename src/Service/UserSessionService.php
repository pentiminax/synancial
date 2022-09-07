<?php

namespace App\Service;

use App\Model\Dashboard\DashboardData;
use App\Model\TimestampedInterface;
use App\Model\Wallet\Checking\CheckingData;
use App\Model\Wallet\Loans\LoansData;
use App\Model\Wallet\Savings\SavingsData;
use App\Model\Wallet\WalletData;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserSessionService
{
    const CHECKING_DATA_KEY = 'CheckingData';
    const DASHBOARD_DATA_KEY = 'DashboardData';
    const LOANS_DATA_KEY = 'LoansData';
    const SAVINGS_DATA_KEY = 'SavingsData';
    const WALLET_DATA_KEY = 'WalletData';

    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function getCheckingData(): ?CheckingData
    {
        $data = $this->getSession()->get(self::CHECKING_DATA_KEY);

        if (!$data || $this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setCheckingData(CheckingData $data): void
    {
        $this->getSession()->set(self::CHECKING_DATA_KEY, $data);
    }

    public function getDashboardData(): ?DashboardData
    {
        $data = $this->getSession()->get(self::DASHBOARD_DATA_KEY);

        if (!$data || $this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setDashboardData(?DashboardData $data): void
    {
        $this->getSession()->set(self::DASHBOARD_DATA_KEY, $data);
    }

    public function getLoansData(): ?LoansData
    {
        $data = $this->getSession()->get(self::LOANS_DATA_KEY);

        if (!$data || $this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setLoansData(?LoansData $data): void
    {
        $this->getSession()->set(self::LOANS_DATA_KEY, $data);
    }

    public function getSavingsData(): ?SavingsData
    {
        $data = $this->getSession()->get(self::SAVINGS_DATA_KEY);

        if (!$data || $this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setSavingsData(?SavingsData $data): void
    {
        $this->getSession()->set(self::SAVINGS_DATA_KEY, $data);
    }

    public function getWalletData(): ?WalletData
    {
        $data = $this->getSession()->get(self::WALLET_DATA_KEY);

        if (!$data || $this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setWalletData(?WalletData $data): void
    {
        $this->getSession()->set(self::WALLET_DATA_KEY, $data);
    }

    public function clear(): void
    {
        $this->getSession()->clear();
    }

    private function isDataExpired(TimestampedInterface $data): bool
    {
        $isDataExpired = false;

        $createdAt = $data->getCreatedAt();

        $now = new \DateTime();

        if ($createdAt->diff($now)->h > 1) {
            $isDataExpired = true;
        }

        return $isDataExpired;
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}