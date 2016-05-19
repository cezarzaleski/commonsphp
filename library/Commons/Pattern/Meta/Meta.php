<?php

namespace Commons\Pattern\Meta;

use Commons\Exception\InvalidArgumentException;

/**
 * Classe responsável pelas operações de verificação do MetaObject.
 */
final class Meta
{
    /**
     * Retorna a instância que está envolvida pelo MetaObject.
     *
     * @param MetaObject $meta
     * @return mixed
     */
    public static function unwrap(MetaObject $meta)
    {
        $wrappedPropertyRef = new \ReflectionProperty($meta, 'wrapped');
        $wrappedPropertyRef->setAccessible(true);
        return $wrappedPropertyRef->getValue($meta);
    }

    /**
     * Verifica se um objeto é do tipo MetaObject
     *
     * @param mixed $object
     * @return boolean
     */
    public static function isMeta($object)
    {
        return ($object instanceof MetaObject);
    }

    /**
     * Método auxiliar para alterar a interface de um objeto para MetaObject.
     *
     * @param mixed $object
     * @return \Commons\Pattern\Meta\MetaObject
     */
    public static function cast($object)
    {
        if (!Meta::isMeta($object)) {
            throw new InvalidArgumentException('Object instance is not a MetaObject.');
        }
        return $object;
    }
}
