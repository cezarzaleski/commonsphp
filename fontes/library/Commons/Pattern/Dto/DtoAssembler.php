<?php
namespace Commons\Pattern\Dto;

use Commons\Exception\InvalidArgumentException;

/**
 * Classe base de Dto.
 *
 * @category Commons
 * @package Commons\Pattern\Dto
 */
abstract class DtoAssembler
{

    /**
     *
     * @param \stdClass|\stdClass[]|array $data
     * @param string $classFullName
     * @param array $cache
     * @throws Commons\Exception\InvalidArgumentException
     * @return \Commons\Pattern\Dto\BaseDto | \Commons\Pattern\Dto\BaseDto[]
     */
    public static function create($data, $classFullName, &$cache = array())
    {
        if (! class_exists($classFullName, true)) {
            throw new InvalidArgumentException(
                vsprintf(
                    'Classe DTO %s não existe.',
                    array($classFullName)
                )
            );
        }

        if (! is_subclass_of($classFullName, 'Commons\\Pattern\\Dto\\BaseDto')) {
            throw new InvalidArgumentException(
                vsprintf(
                    'Classe DTO %s inválida.',
                    array($classFullName)
                )
            );
        }

        if (is_array($data)) {
            // cria lista de objetos
            $return = array();
            foreach ($data as $singleData) {
                $return[] = new $classFullName($singleData, array(), array(), $cache);
            }
        } else {
            // cria objeto simples
            $return = new $classFullName($data, array(), array(), $cache);
        }

        return $return;
    }
}
