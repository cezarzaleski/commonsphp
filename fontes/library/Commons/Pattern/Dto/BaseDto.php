<?php

namespace Commons\Pattern\Dto;

use Commons\Pattern\Dto\DtoAssembler;
use Commons\Exception\InvalidArgumentException;
use Commons\Util\Reflect\ReflectionUtil;
use Commons\Pattern\Data\Datum;

/**
 * Classe base de Dto.
 *
 * @category Commons
 * @package Commons\Pattern\Dto
 */
 abstract class BaseDto implements Datum
{

    /**
     * Construtor padrão.
     *
     *  1) Uso padrão: new Class();
     *  2) Uso com importação de dados: new Class($data);
     *  3) Uso com redefinição de aliases: new Class($data, $aliases);
     *  4) Uso com redefinição de mapeamento: new Class($data, $aliases, $mapping);
     *  5) Uso com objetos pré-cacheados: new Class($data, $aliases, $mapping, $cache);
     *      - Observação neste caso as chaves deste cache deve ter o formato 'Tipo\Dado_identificador'
     *          - Facilmente adquiridas pelo código $cache[\get_class($this).'_'.$this->getId()] = $this;
     *      - No entanto não é um uso recomendado na maioria dos casos.
     *
     * @param \stdClass|array $data Quando alguma propriedade estiver no formato array é recomendado exista
     *                              pelo menos uma chave 'id' para evitar dependências cíclicas.
     *                              Um identificador é utilizado no caching de objetos no processo de importação
     *                              de dados.
     *                              Para evitar o uso do processo de importação de dados utilizar o uso padrão 1,
     *                              ou qualquer outro uso mas passando em $data o valor 'null'.
     * @param array $aliases    Ex: array( 'propriedade' => 'outro_nome', );
     * @param array $propertyMapping Ex: array( 'propriedade' => 'Classe\\Da\\Propriedade', );
     * @param array $cache
     */
    public function __construct(
        $data = null,
        array $aliases = array(),
        array $propertyMapping = array(),
        array &$cache = array()
    ) {
        $this->import($data, $aliases, $propertyMapping, $cache);
    }

    /**
     * @param \stdClass|array $data
     * @throws InvalidArgumentException
     * @return array
     */
    private function retrieveRawData($data)
    {
        $rawData = $data;
        if (! \is_array($data)) {
            if (\is_object($data) && ! \is_callable($data)) {
                $rawData = ReflectionUtil::deepPropertyExtractor($data);
            } else {
                throw new InvalidArgumentException('Dados de inicialização de Dto inválidos.');
            }
        }
        return $rawData;
    }

    /**
     * @param \stdClass|array $data
     * @param array $aliases
     * @return string
     */
    private function retrieveIdentifierName($data, $aliases)
    {
        $idName = 'id';
        if (! isset($data['id']) && \array_key_exists('id', $aliases)) {
            $idName = $aliases['id'];
        }
        return $idName;
    }

    /**
     * @param \stdClass|array $data
     * @param array $aliases
     * @param array $cache
     */
    private function registerInCache($data, $aliases, &$cache)
    {
        $idName = $this->retrieveIdentifierName($data, $aliases);

        if (isset($data[$idName])) {
            $cache[\get_class($this) . '_' . $data[$idName]] = $this;
        }
    }

    /**
     * @param \stdClass|array $data
     * @param array $aliases    Ex: array( 'propriedade' => 'outro_nome', );
     * @param array $propertyMapping Ex: array( 'propriedade' => 'Classe\\Da\\Propriedade', );
     * @param array $cache
     */
    public function import($data, $aliases = array(), $propertyMapping = array(), &$cache = array())
    {
        if ($data !== null && ! \is_scalar($data)) {
            $data = $this->retrieveRawData($data);

            if (\count($data)) {
                $this->registerInCache($data, $aliases, $cache);

                foreach ($data as $attribute => $value) {
                    $cache = $this->importProperty($attribute, $aliases, $propertyMapping, $value, $cache);
                }
            }
        }
    }

    /**
     * Importa a propriedade.
     *
     * @param mixed $attribute
     * @param array $aliases    Ex: array( 'propriedade' => 'outro_nome', );
     * @param array $propertyMapping Ex: array( 'propriedade' => 'Classe\\Da\\Propriedade', );
     * @param mixed $value
     * @param array $cache
     * @return array (Cache)
     */
    private function importProperty($attribute, array $aliases, array $propertyMapping, $value, array $cache)
    {
        if (\in_array($attribute, $aliases)) {
            $attribute = \array_search($attribute, $aliases);
        }
        if (\property_exists($this, $attribute)) {
            if (\array_key_exists($attribute, $propertyMapping)) {
                $value = (array) $value;
                $className = $propertyMapping[$attribute];
                if (isset($value['id']) && \array_key_exists($className . '_' . $value['id'], $cache)) {
                    $this->$attribute = $cache[$className . '_' . $value['id']];
                } else {
                    $this->$attribute = DtoAssembler::create((object) $value, $propertyMapping[$attribute], $cache);
                }
            } else {
                $this->$attribute = $value;
            }
        }
        return $cache;
    }

    /**
     * {@inheritDoc}
     * @see \Commons\Pattern\Data\Datum::toArray()
     */
    public function toArray()
    {
        return $this->retrieveRawData($this);
    }


    /**
     * Para classes que herdam de BaseDto o processo de importação através da interface Datum deve seguir o seguinte
     * padrão:
     *
     * array (
     *  'data' => array(),
     *  'aliases' => array(),
     *  'mapping' => array(),
     *  'cache' => array()
     * );
     *
     * Sendo a chave 'data' a única obrigatória.
     *
     * {@inheritDoc}
     * @see \Commons\Pattern\Data\Datum::fromArray()
     */
    public function fromArray(array $options)
    {
        $dataKey = 'data';
        if (isset($options[$dataKey]) && \is_array($options[$dataKey])) {
            $getValueOrDefault = function ($options, $key) {
                return isset($options[$key]) && !\is_null($options[$key]) ? $options[$key] : array();
            };

            $data = $getValueOrDefault($options, $dataKey);
            $aliases = $getValueOrDefault($options, 'aliases');
            $propertyMapping = $getValueOrDefault($options, 'mapping');
            $cache = $getValueOrDefault($options, 'cache');

            $this->import($data, $aliases, $propertyMapping, $cache);
        } else {
            throw new InvalidArgumentException("O formato das opções está incorreto, o campo 'data' não é um array.");
        }
    }
}
