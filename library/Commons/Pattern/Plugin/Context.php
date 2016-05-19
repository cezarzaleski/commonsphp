<?php

namespace Commons\Pattern\Plugin;

/**
 * Representa o contexto de execução dos plugins (nesse caso é o escopo de uma função).
 *
 * @category Commons
 * @package Commons\Pattern\Plugin
 */
class Context
{
    /**
     * Se o tipo da operação for método.
     *
     * @var integer
     */
    const OP_METHOD = 1;

    /**
     * Se o tipo da operação for propriedade.
     *
     * @var integer
     */
    const OP_PROPERTY = 2;

    /**
     *
     * @var mixed
     */
    private $exception = null;

    /**
     *
     * @var mixed
     */
    private $result = null;

    /**
     *
     * @var array
     */
    private $params = array();

    /**
     *
     * @var boolean
     */
    private $lock = false;

    /**
     *
     * @var string
     */
    private $operation;

    /**
     *
     * @var integer
     */
    private $operationType;

    /**
     *
     * @var mixed
     */
    private $owner;

    /**
     * @var \Commons\Pattern\Plugin\Pluggable $pluggable
     */
    private $pluggable;

    /**
     * @param \Commons\Pattern\Plugin\Pluggable $pluggable
     * @param mixed $owner
     * @param string $operation
     * @param array $params
     */
    public function __construct(
        $pluggable,
        $owner,
        $operation,
        array $params = array(),
        $operationType = Context::OP_METHOD
    ) {
        $this->pluggable = $pluggable;
        $this->owner = $owner;
        $this->operation = $operation;
        $this->params = $params;
        $this->operationType = $operationType;
    }

    /**
     * @return \Commons\Pattern\Plugin\Pluggable
     */
    public function getPluggable()
    {
        return  $this->pluggable;
    }

    /**
     *
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     *
     * @return integer
     */
    public function getOperationType()
    {
        return $this->operationType;
    }

    /**
     *
     * @return mixed
     */
    public function &getParams()
    {
        return $this->params;
    }

    /**
     *
     * @return mixed
     */
    public function getReturn()
    {
        return $this->result;
    }

    /**
     *
     * @param mixed $result
     * @return \Commons\Pattern\Plugin\Context Provê interface fluente.
     */
    public function setReturn($result)
    {
        $this->result = $result;
        return $this;
    }

    /**
     *
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }

    /**
     *
     * @param \Exception $result
     * @return \Commons\Pattern\Plugin\Context Provê interface fluente.
     */
    public function setException($exception)
    {
        $this->exception = $exception;
        return $this;
    }

    /**
     * Relança a exceção capturada pelo contexto.
     */
    public function rethrowException()
    {
        if ($this->exception) {
            throw $this->exception;
        }
    }

    /**
     *
     * @return boolean
     */
    public function isLocked()
    {
        return $this->lock;
    }

    /**
     *
     * @param boolean $lock
     * @return \Commons\Pattern\Plugin\Context
     */
    public function setLock($lock)
    {
        $this->lock = $lock;
        return $this;
    }
}
