<?php
declare(strict_types=1);

use Wumvi\JsonRpc\ModelResponse;

class ModelResponse extends ModelResponse
{
    /**
     * @var string
     */
    private $result;

    public function setResult(string $value): void
    {
        $this->result = $value;
    }

    public function getResult(): string
    {
        return $this->result;
    }

    public function jsonSerialize()
    {
        return [
            'name' => $this->result
        ];
    }
}
