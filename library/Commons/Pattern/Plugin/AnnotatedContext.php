<?php

namespace Commons\Pattern\Plugin;

use Commons\Pattern\Plugin\Context;

/**
 * Contexto que carrega as anotações.
 */
class AnnotatedContext extends Context
{

    /**
     * Anotações do contexto.
     *
     * @var array
     */
    private $annotations = array();

    /**
     * Recupera as anotações.
     *
     * @return array
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Insere o array de anotações.
     *
     * @param array $annotations
     * @return \Commons\Pattern\Meta\Plugin\AnnotatedContext
     */
    public function setAnnotations(array $annotations)
    {
        $this->annotations = $annotations;
        return $this;
    }
}
