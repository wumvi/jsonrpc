<?php
declare(strict_types=1);

class ModelRequest extends ModelSession
{
    public function getWord(): string
    {
        return $this->raw['word'];
    }
}
