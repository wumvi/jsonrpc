<?php
declare(strict_types=1);

namespace Wumvi\JsonRpc;

abstract class ModelOut implements \JsonSerializable
{
    private $errorMsg = '';
    private $errorCode = '';

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
}
