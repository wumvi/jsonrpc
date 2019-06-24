<?php
declare(strict_types=1);

namespace Wumvi\JsonRpc;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class Init
{
    private const ERROR_WRONG_JSON = 1;
    private const ERROR_CALL_NOT_FOUND = 2;
    private const ERROR_CONTROLLER_NOT_FOUND = 3;
    private const ERROR_METHOD_NOT_FOUND = 4;
    private const ERROR_MODEL_IN_NOT_FOUND = 5;
    private const ERROR_MODEL_OUT_NOT_JSON_SERIALIZABLE = 6;
    private const JSON_RPC_VERSION = '2.0';

    /**
     * @param string $diConfig
     *
     * @return ContainerBuilder
     *
     * @throws \Exception
     */
    public static function getDi(string $diConfig): ContainerBuilder
    {
        $di = new ContainerBuilder();
        $loader = new YamlFileLoader($di, new FileLocator(__DIR__));
        $loader->load($diConfig);

        return $di;
    }

    /**
     * @param string $requestData
     *
     * @return JsonCall[]
     *
     * @throws \Exception
     */
    public static function getCalls(string $requestData): array
    {
        $result = [];
        $json = json_decode($requestData, true);
        if ($json === null || $json === false) {
            throw new \Exception('Wrong json. Parse error', self::ERROR_WRONG_JSON);
        }
        if (is_array($json)) {
            foreach ($json as $item) {
                $result[] = new JsonCall($item);
            }
        } else {
            $result[] = new JsonCall($json);
        }

        return $result;
    }

    /**
     * @param JsonCall $call Модель с параметрами вызова
     * @param array $routes Массив роутев
     * @param ContainerBuilder $di DI
     * @param callable $beforeRequest Callback Вызывается перед запросом.
     *  Должен вернуть или \Wumvi\JsonRpc\ModelResponse или null
     *
     * @return \JsonSerializable|null Ответ по запросу
     *
     * @throws \Exception
     */
    public static function call(
        JsonCall $call,
        array $routes,
        ContainerBuilder $di,
        callable $beforeRequest = null
    ): ?ModelResponse {
        if (!array_key_exists($call->getMethod(), $routes)) {
            $name = htmlspecialchars($call->getMethod());
            throw new \Exception(
                'RPC by ' . $name . ' not found',
                self::ERROR_CALL_NOT_FOUND
            );
        }

        $route = $routes[$call->getMethod()];
        $controller = $route[CONTROLLER];
        if (!class_exists($controller)) {
            throw new \Exception(
                'Class for controller ' . htmlspecialchars($controller) . ' not found in route config',
                self::ERROR_CONTROLLER_NOT_FOUND
            );
        }
        $controller = new $controller();
        $method = $route[METHOD];
        if (!method_exists($controller, $method)) {
            throw new \Exception(
                'Class for method ' . htmlspecialchars($method) . ' not found in controller',
                self::ERROR_METHOD_NOT_FOUND
            );
        }
        $model = $route[MODEL];
        if (!class_exists($model)) {
            throw new \Exception(
                'Class for model ' . htmlspecialchars($model) . ' not found',
                self::ERROR_MODEL_IN_NOT_FOUND
            );
        }
        $settings = $route[SETTINGS] ?? null;
        $result = null;
        if ($beforeRequest) {
            $result = $beforeRequest(new $model($call->getParams()), $di, $settings);
        }
        if ($result === null) {
            $result = $controller->$method(new $model($call->getParams()), $di, $settings);
        }
        if ($result === null) {
            return null;
        }

        if (!($result instanceof \JsonSerializable)) {
            throw new \Exception(
                'Out model is not instance of JsonSerializable',
                self::ERROR_MODEL_OUT_NOT_JSON_SERIALIZABLE
            );
        }

        return $result;
    }

    /**
     * Формирует ошибку
     *
     * @param string $msg Сообщение об ошибке
     * @param string $code Код ошибки
     * @param string|null $id ID запроса, если есть
     *
     * @return array Данные об ошибке
     */
    public static function makeErrorResult(string $msg, string $code, ?string $id): array
    {
        return [
            'jsonrpc' => self::JSON_RPC_VERSION,
            'error' => [
                'message' => $msg,
                'code' => $code
            ],
            'id' => $id,
        ];
    }

    /**
     * @param array $routes Массив роутов
     * @param ContainerBuilder $di DI
     * @param string $requestMethod Вызываемый метод
     * @param string $requestData Данные запроса
     * @param callable|null $beforeRequest Callback Вызывается перед запросом.
     *      Должен вернуть или \Wumvi\JsonRpc\ModelResponse или null
     *
     * @return string Результат запрос
     */
    public static function getResponseJson(
        array $routes,
        ContainerBuilder $di,
        string $requestMethod,
        string $requestData,
        callable $beforeRequest = null
    ): string {
        if ($requestMethod !== 'POST') {
            $json = self::makeErrorResult('Method is not POST', 'method-is-not-post', null);
            return json_encode($json);
        }

        $json = [];
        try {
            $calls = self::getCalls($requestData);
        } catch (\Throwable $ex) {
            return json_encode(self::makeErrorResult($ex->getMessage(), $ex->getCode() . '', null));
        }
        for ($index = 0; $index < count($calls); $index++) {
            $call = $calls[$index];
            try {
                $result = self::call($call, $routes, $di, $beforeRequest);
                if ($result->getErrorCode() !== '') {
                    $json[] = self::makeErrorResult(
                        $result->getErrorMsg(),
                        $result->getErrorCode(),
                        $call->getId()
                    );
                } else {
                    $json[] = [
                        'jsonrpc' => self::JSON_RPC_VERSION,
                        'result' => $result,
                        'id' => $call->getId(),
                    ];
                }
            } catch (\Throwable $ex) {
                $json[] = self::makeErrorResult($ex->getMessage(), $ex->getCode() . '', $call->getId());
            }
        }

        return json_encode($json);
    }
}
