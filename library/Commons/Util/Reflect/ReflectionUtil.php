<?php

namespace Commons\Util\Reflect;

class ReflectionUtil
{

    /**
     * Extrai todas as propriedades em formato de array.
     * E se as propriedades tiverem objetos serão convertidos em arrays.
     *
     * @param object $obj
     * @return array
     */
    public static function deepPropertyExtractor($obj)
    {
        return static::deepPropertyExtractorResolver(null, $obj);
    }

    private static function deepPropertyExtractOrReference($arrCache, $value)
    {
        $result = null;
        if ($value && is_object($value) && array_key_exists(\spl_object_hash($value), $arrCache)) {
            $result = &$arrCache[\spl_object_hash($value)];
        } else {
            if (is_object($value)) {
                if ($value instanceof \DateTimeInterface) {
                    $result = $value->format(\DateTime::ISO8601);
                } else {
                    $val = static::deepPropertyExtractorResolver($arrCache, $value);
                    $arrCache[\spl_object_hash($value)] = $val;
                    $result = $val;
                }
            } else {
                $result = $value;
            }
        }
        return $result;
    }

    private static function deepPropertyExtractorResolver($arrCache, $obj)
    {
        if (! $arrCache) {
            $arrCache = array();
        }
        $arr = null;

        if ($obj && is_object($obj)) {
            $arr = array();
            if (! array_key_exists(\spl_object_hash($obj), $arrCache)) {
                $arrCache[\spl_object_hash($obj)] = &$arr;
            }
            $properties = array();
            if ($obj instanceof \stdClass) {
                $properties = (array) $obj;
                foreach ($properties as $name => $value) {
                    $arr[$name] = static::deepPropertyExtractOrReference($arrCache, $value);
                }
            } else {
                $reflect = new \ReflectionClass($obj);
                $properties = $reflect->getProperties();
                foreach ($properties as $property) {
                    $property->setAccessible(true);
                    $value = $property->getValue($obj);
                    $name = $property->getName();
                    $property->setAccessible(false);
                    $arr[$name] = static::deepPropertyExtractOrReference($arrCache, $value);
                }
            }
        }
        return $arr;
    }

    /**
     * Converte arrays em objetos \stdClass();
     *
     * @param array $arr
     * @return \stdClass
     */
    public static function arrayToObject($arr)
    {
        if (is_array($arr)) {
            return (object)\array_map(
                array('Commons\Util\Reflect\ReflectionUtil', __FUNCTION__),
                $arr
            );
        } else {
            return $arr;
        }
    }

    /**
     * Extrai todas as propriedades para o formato de array.
     *
     * @param object $obj
     * @return array
     */
    public static function propertyExtractor($obj)
    {
        $arr = array();
        if ($obj) {
            $reflect = new \ReflectionClass($obj);
            $properties = $reflect->getProperties();

            foreach ($properties as $property) {
                $property->setAccessible(true);
                $value = $property->getValue($obj);
                $arr[$property->getName()] = $value;
                $property->setAccessible(false);
            }
        }
        return $arr;
    }

    /**
     * Clona um valor de uma propriedade.
     *
     * @param object $targetPropertyValue
     * @param object $fromPropertyValue
     * @param array $mapping
     * @param array $notCopy
     * @return Ambigous object
     */
    private static function cloneProperty($targetPropertyValue, $fromPropertyValue, $mapping, $notCopy)
    {
        $result = null;
        if ($targetPropertyValue && is_object($targetPropertyValue) && $fromPropertyValue &&
            is_object($fromPropertyValue)) {
            static::copyProperties($targetPropertyValue, $fromPropertyValue, $mapping, $notCopy);
            $result = $targetPropertyValue;
        } else {
            if ($fromPropertyValue && is_object($fromPropertyValue)) {
                $result = clone $fromPropertyValue;
            } else {
                $result = $fromPropertyValue;
            }
        }
        return $result;
    }

    /**
     * Copia as propriedades do objeto $from para o objeto $target,
     * podendo ser utilizado um mapeamento $mapping de propriedades
     * array($class => array($targetProperty => $fromProperty),).
     *
     * @param object|\stdClass $target
     * @param object|\stdClass|array $from
     * @param array $mapping
     */
    public static function copyProperties($target, $from, $mapping = array())
    {
        // para evitar a cópia cíclica
        $notCopy = (func_num_args() == 4 && func_get_arg(3)) ? func_get_arg(3) : array();
        $from = (is_array($from)) ? static::arrayToObject($from) : $from;
        if (static::isObject($target) && ! in_array(\spl_object_hash($target), $notCopy)
            && static::isObject($from)) {
            $notCopy[] =\spl_object_hash($target);
            $mappingClass = isset($mapping[get_class($target)]) ? $mapping[get_class($target)] : array();
            $isTargetStd = ($target instanceof \stdClass);
            $reflectFrom = new \ReflectionClass($from);

            if ($isTargetStd) {
                $targetProperties = (array) $target;
                foreach ($targetProperties as $targetPropertyName => $targetPropertyValue) {
                    $fromPropertyName = isset($mappingClass[$targetPropertyName]) ? $mappingClass[$targetPropertyName]
                                                                                  : $targetPropertyName;
                    if (isset($from->$fromPropertyName)) {
                        $fromPropertyValue = $from->$fromPropertyName;
                        $target->$targetPropertyName =
                            static::cloneProperty($targetPropertyValue, $fromPropertyValue, $mapping, $notCopy);
                    } elseif ($reflectFrom->hasProperty($fromPropertyName)) {
                        $fromProperty = $reflectFrom->getProperty($fromPropertyName);
                        $fromProperty->setAccessible(true);
                        $fromPropertyValue = $fromProperty->getValue($from);
                        $target->$targetPropertyName =
                            static::cloneProperty($targetPropertyValue, $fromPropertyValue, $mapping, $notCopy);
                        $fromProperty->setAccessible(false);
                    }
                }
            }

            $reflectTarget = new \ReflectionClass($target);
            $targetProperties = $reflectTarget->getProperties();

            foreach ($targetProperties as $targetProperty) {
                $fromPropertyName = isset($mappingClass[$targetProperty->getName()])
                                    ? $mappingClass[$targetProperty->getName()] : $targetProperty->getName();
                $targetProperty->setAccessible(true);
                $targetPropertyValue = $targetProperty->getValue($target);
                if (isset($from->$fromPropertyName)) {
                    $fromPropertyValue = $from->$fromPropertyName;
                    $targetProperty->setValue(
                        $target,
                        static::cloneProperty($targetPropertyValue, $fromPropertyValue, $mapping, $notCopy)
                    );
                } elseif ($reflectFrom->hasProperty($fromPropertyName)) {
                    $fromProperty = $reflectFrom->getProperty($fromPropertyName);
                    $fromProperty->setAccessible(true);
                    $fromPropertyValue = $fromProperty->getValue($from);
                    $targetProperty->setValue(
                        $target,
                        static::cloneProperty($targetPropertyValue, $fromPropertyValue, $mapping, $notCopy)
                    );
                    $fromProperty->setAccessible(false);
                }
                $targetProperty->setAccessible(false);
            }
        }
    }

    /**
     * Verifica se é um objeto.
     *
     * @param mixed $target
     * @return boolean
     */
    private static function isObject($target)
    {
        return $target && is_object($target);
    }


    /**
     * Método responsável por retornar um array com as referências para os
     * valores passados em $source.
     *
     * @param array $source
     * @return array referências
     */
    public static function getArrayReference(array $source)
    {
        $references = array();
        foreach ($source as $key => &$data) {
            $references[$key] = &$data;
        }
        return $references;
    }
}
