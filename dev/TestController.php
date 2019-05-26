<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\ContainerBuilder;

class TestController
{
    public function doSomeAction(ModelRequest $in, ContainerBuilder $di): ModelResponse
    {

        $result = new ModelResponse();
        $result->setResult($in->getWord());
        // $result->setError('ddd', '3');

        return $result;
    }
}
