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
    public function __construct($pluggable, $owner, $operation, array $params = array())
    {
        $this->pluggable = $pluggable;
        $this->owner = $owner;
        $this->operation = $operation;
        $this->params = $params;
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
