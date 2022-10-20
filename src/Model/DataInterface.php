<?php

namespace App\Model;

interface DataInterface
{
    public function getDistribution(): Distribution;

    public function setDistribution(Distribution $distribution): self;
}