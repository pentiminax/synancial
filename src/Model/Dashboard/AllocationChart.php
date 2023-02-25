<?php

namespace App\Model\Dashboard;

class AllocationChart
{
    private array $labels;

    public function __construct()
    {
        $this->labels = [
            'Compte bancaires',
            "Comptes d'investissements",
            'Livrets',
            'Crowdlending',
        ];
    }

    public function getLabels(): array
    {
        return $this->labels;
    }
}