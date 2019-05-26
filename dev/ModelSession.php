<?php
declare(strict_types=1);


abstract class ModelSession
{
    protected $raw;

    public function __construct($raw)
    {
        $this->raw = $raw;
    }

    public function getSession(): string
    {
        return $this->raw['session'];
    }
}
