<?php

namespace App\Model;

interface ViewDataInterface
{
    public function getCreatedAt(): \DateTime;

    public function setCreatedAt(\DateTime $createdAt): self;
}