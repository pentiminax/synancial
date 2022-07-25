<?php

namespace App\Model;

interface TimestampedInterface
{
    public function getCreatedAt(): \DateTime;

    public function setCreatedAt(\DateTime $createdAt): self;
}