<?php
declare(strict_types=1);

namespace Wumvi\JsonRpc;


class JsonCall
{
    public const ID = 'id';
    public const METHOD = 'method';
    public const PARAMS = 'params';
    private $json = [];

    /**
     * JsonCall constructor.
     *
     * @param array $json
     *
     * @throws \Exception
     */
    public function __construct(array $json)
    {
        if (!array_key_exists(JsonCall::METHOD, $json)) {
            throw new \Exception('Key method not found', 2);
        }

        if (!array_key_exists(JsonCall::PARAMS, $json)) {
            throw new \Exception('Key method not found', 2);
        }

        if (!array_key_exists(JsonCall::ID, $json)) {
            throw new \Exception('Key method not found', 2);
        }

        $this->json = $json;
    }

    public function getId(): string
    {
        return $this->json[self::ID];
    }

    public function getMethod(): string
    {
        return $this->json[self::METHOD];
    }

    public function getParams(): array
    {
        return $this->json[self::PARAMS];
    }
}
