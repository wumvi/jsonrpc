<?php
declare(strict_types=1);

class ModelError extends \Wumvi\JsonRpc\ModelResponse
{
    public function jsonSerialize()
    {
        return [];
    }
}
