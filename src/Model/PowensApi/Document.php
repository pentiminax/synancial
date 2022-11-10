<?php

namespace App\Model\PowensApi;

class Document
{
    public int $id;

    public int $id_type;

    public int $id_user;

    public int $id_subscription;

    public int $id_file;

    public int $id_thumbnail;

    public string $name;

    public \DateTime $date;

    public float $total_amount;

    public string $webid;

    public bool $has_file_on_website;

    public string $type;

    public string $url;

    public string $thumb_url;
}