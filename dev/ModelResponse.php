<?php
declare(strict_types=1);

use Wumvi\JsonRpc\ModelOut;

class ModelResponse extends ModelOut
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
