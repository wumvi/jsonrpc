<?php
declare(strict_types=1);

class ModelError extends \Wumvi\JsonRpc\ModelOut
{
    public function jsonSerialize()
    {
        return [];
    }
}
