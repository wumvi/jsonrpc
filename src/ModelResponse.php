<?php
declare(strict_types=1);

namespace Wumvi\JsonRpc;

class ModelResponse implements \JsonSerializable
{
    private $errorMsg = '';
    private $errorCode = '';
    protected $raw = [];

    public function setError(string $code, string $msg): void
    {
        $this->errorMsg = $msg;
        $this->errorCode = $code;
    }

    public function getErrorMsg(): string
    {
        return $this->errorMsg;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function jsonSerialize()
    {
        return $this->raw;
    }
}
