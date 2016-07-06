<?php

namespace Commons\Pattern\Meta\Plugin;

use Commons\Pattern\Plugin\Impl\AbstractAnnotationExtractor;
use Commons\Pattern\Cache\Cache;
use Commons\Pattern\Plugin\Context;

/**
 * Atribui operações a métodos da classe em cada fase de execução do método envolvido.
 */
class MetaAnnotation extends AbstractAnnotationExtractor
{

    /**
     * Construtor padrão.
     *
     * @param Cache $cache
     */
    public function __construct(Cache $cache = null)
    {
        parent::__construct(
            array(
                'Commons\Pattern\Meta\Annotation' => __DIR__ . '/../../../../../library'
            ),
            $cache
        );
    }

    /**
     * Processa anotações do tipo Before, After, OnException e Last.
     *
     * @param Context $context
     * @param string $type
     */
    protected function processAnnotations(Context $context, $type)
    {
        $annoContext = $this->getAnnotatedContext($context);

        foreach ($annoContext->getAnnotations() as $annotation) {
            if ($annoContext->isLocked()) {
                break;
            }
            if (\is_a($annotation, $type)) {
                $owner = $context->getOwner();
                $operation = $annotation->value;
                $method = new \ReflectionMethod($owner, $operation);
                $method->setAccessible(true);
                $method->invoke($owner, $annoContext);
            }
        }

        $context->setException($annoContext->getException());
        $context->setLock($annoContext->isLocked());
        $context->setReturn($annoContext->getReturn());
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::preDispatch()
     */
    public function preDispatch(Context $context)
    {
        $this->processAnnotations($context, '\Commons\Pattern\Meta\Annotation\Before');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Plugin::postDispatch()
     */
    public function postDispatch(Context $context)
    {
        $this->processAnnotations($context, '\Commons\Pattern\Meta\Annotation\After');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Impl\Standard::errorDispatch()
     */
    public function errorDispatch(Context $context)
    {
        $this->processAnnotations($context, '\Commons\Pattern\Meta\Annotation\OnException');
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Plugin\Impl\Standard::finallyDispatch()
     */
    public function finallyDispatch(Context $context)
    {
        $this->processAnnotations($context, '\Commons\Pattern\Meta\Annotation\Last');
    }
}
