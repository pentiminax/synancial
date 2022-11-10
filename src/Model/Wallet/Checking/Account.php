<?php

namespace App\Model\Wallet\Checking;

use App\Model\PowensApi\BankAccount;

class Account
{
    private int $id;

    private string $name;

    private float $balance;

    private Bank $bank;

    public function __construct(?BankAccount $bankAccount = null)
    {
        $this->bank = new Bank();

        if ($bankAccount) {
            $this->id = $bankAccount->id;
            $this->name = $bankAccount->name;
            $this->balance = $bankAccount->balance;
        }
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    public function getBank(): Bank
    {
        return $this->bank;
    }

    public function setBank(Bank $bank): void
    {
        $this->bank = $bank;
    }
}