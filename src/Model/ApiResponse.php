<?php

namespace App\Model;

class ApiResponse
{
    private ?string $error = null;

    private string $message = 'OK';

    private $result;

    public function __construct(?string $error = null, string $message = 'OK', $result = null)
    {
        $this->error = $error;
        $this->message = $message;

        if($result) {
            $this->result = $result;
        }
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getResult()
    {
        return $this->result;
    }

    public function setResult($result): void
    {
        $this->result = $result;
    }
}