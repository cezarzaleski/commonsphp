<?php

namespace CommonsTest\Pattern\Service;

use Commons\Pattern\Service\Impl\AbstractCoreService;

class ExemploService extends AbstractCoreService
{
    public function operacaoHelloWorld()
    {
        $this->getLogger()->info('Sending hello world message.');
        return 'Hello World.';
    }

    public function operacaoHelloWorldFromOtherService()
    {
        $service = $this->getHelloService();
        return ($service) ? $service->operacaoHelloWorld() : "Nothing for you.";
    }

    /**
     * @return ExemploService
     */
    protected function getHelloService()
    {
        return $this->getLookupManager()->get('helloService');
    }
}
