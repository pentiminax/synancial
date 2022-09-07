<?php

namespace App\Model;

class BankAccount
{
    public int $id;

    public ?int $id_connection;

    public ?int $id_user;

    public ?int $id_source;

    public ?int $id_parent;

    public ?string $number;

    public string $webid;

    public string $original_name;

    public float $balance;

    public ?float $coming;

    public bool $display;

    public ?\DateTime $last_update;

    public ?\DateTime $deleted;

    public ?\DateTime $disabled;

    public ?string $iban;

    public $currency;

    public $id_type;

    public int $bookmarked;

    public string $name;

    public ?string $error;

    public $usage;

    public $ownership;

    public ?string $company_name;

    public ?\DateTime $opening_date;

    public ?string $bic;

    public float $coming_balance;

    public string $formatted_balance;

    public string $type;

    public $information;

    public ?array $loan;
}