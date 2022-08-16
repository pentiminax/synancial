<?php

namespace App\Service;

use App\Model\Dashboard\DashboardData;
use App\Model\TimestampedInterface;
use App\Model\Wallet\Checking\CheckingData;
use App\Model\Wallet\Savings\SavingsData;
use App\Model\Wallet\WalletData;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserSessionService
{
    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function getCheckingData(): ?CheckingData
    {
        $session = $this->getSession();

        $data = $session->get('CheckingData');

        if (!$data) {
            return null;
        }

        if ($this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setCheckingData(CheckingData $data): void
    {
        $this->getSession()->set('CheckingData', $data);
    }

    public function getDashboardData(): ?DashboardData
    {
        $data = $this->getSession()->get('DashboardViewData');

        if (!$data || $this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setDashboardData(?DashboardData $data): void
    {
        $this->getSession()->set('DashboardViewData', $data);
    }

    public function getSavingsData(): ?SavingsData
    {
        $data = $this->getSession()->get('SavingsViewData');

        if (!$data || $this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setSavingsData(?SavingsData $data): void
    {
        $data = $this->getSession()->set('SavingsViewData', $data);
    }

    public function getWalletData(): ?WalletData
    {
        $data = $this->getSession()->get('WalletViewData');

        if (!$data || $this->isDataExpired($data)) {
            return null;
        }

        return $data;
    }

    public function setWalletData(?WalletData $data): void
    {
        $this->getSession()->set('WalletViewData', $data);
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