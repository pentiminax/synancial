<?php

namespace App\Model;

class News
{
    public string $title;

    private string $link;

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = "https://bourse.fortuneo.fr" . $link;

        return $this;
    }
}