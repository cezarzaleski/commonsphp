<?php

namespace Commons\Pattern\Plugin\Impl;

use Commons\Pattern\Cache\Cache as CacheInterface;
use Commons\Pattern\Plugin\Context;
use Commons\Util\Annotation\AnnotationUtil;
use Commons\Pattern\Plugin\AnnotatedContext;

/**
 * Plugin com ferramental para extrair anotações.
 */
abstract class AbstractAnnotationExtractor extends Standard
{
    /**
     * @var Cache
     */
    protected $cache = null;

    /**
     * @var string
     */
    protected $prefix = 'abstract_annotation_extractor_plugin';

    /**
     * Construtor padrão.
     *
     * @param array $namespaceDirs
     * @param Cache $cache [Opcional]
     */
    public function __construct(array $namespaceDirs, CacheInterface $cache = null)
    {
        foreach ($namespaceDirs as $namespace => $dir) {
            AnnotationUtil::loadAnnotationsFromNamespace($namespace, $dir);
        }

        $this->cache = $cache;
    }

    /**
     * Extrai anotações do do contexto.
     *
     * @param Context $context
     */
    protected function extractAnnotations(Context $context)
    {
        $objAnnotations = array();
        if ($context->getOwner()) {
            $objRef = new \ReflectionClass($context->getOwner());
            $objAnnotations = AnnotationUtil::extractAnnotations($objRef, $this->cache);
        }

        $opAnnotations = array();
        if ($context->getOperationType() === Context::OP_METHOD) {
            $methodRef = new \ReflectionMethod($context->getOwner(), $context->getOperation());
            $opAnnotations = AnnotationUtil::extractAnnotations($methodRef, $this->cache);
        } elseif ($context->getOperationType() === Context::OP_PROPERTY) {
            $propertyRef = new \ReflectionProperty($context->getOwner(), $context->getOperation());
            $opAnnotations = AnnotationUtil::extractAnnotations($propertyRef, $this->cache);
        }

        return \array_merge(
            $objAnnotations,
            $opAnnotations
        );
    }

    /**
     * Recupera contexto anotado.
     *
     * @param Context $context
     * @return AnnotatedContext
     */
    protected function getAnnotatedContext(Context $context)
    {
        $annoContext = new AnnotatedContext(
            $context->getPluggable(),
            $context->getOwner(),
            $context->getOperation(),
            $context->getParams(),
            $context->getOperationType()
        );
        $annoContext->setAnnotations($this->extractAnnotations($context));
        $annoContext->setException($context->getException());
        $annoContext->setLock($context->isLocked());
        $annoContext->setReturn($context->getReturn());
        return $annoContext;
    }

    /**
     * Encontra uma anotação de um contexto.
     *
     * @param Context $context
     * @param string $annotationClassName
     * @param boolean $unique
     * @param mixed $default
     * @return mixed|NULL
     */
    protected function findAnnotation(Context $context, $annotationClassName, $unique = false, $default = null)
    {
        $annotations = $this->extractAnnotations($context);
        return AnnotationUtil::findAnnotation($annotationClassName, $annotations, $unique, $default);
    }
}
