<?php
namespace Commons\Pattern\Dto;

use Commons\Pattern\Identifier\Identifiable;
use Commons\Pattern\Identifier\TIdentifiable;

/**
 * Classe base de Dto.
 *
 * @category Commons
 * @package Commons\Pattern\Dto
 */
abstract class Dto extends BaseDto implements Identifiable
{
    use TIdentifiable;

    /**
     * Mapeamento para tipos complexos.
     *
     * @var array Ex: array( 'propriedade' => 'Classe\\Da\\Propriedade', );
     */
    protected $mapping = array();

    /**
     * Mapeamento de alias para propriedades entrantes.
     *
     * @var array Ex: array( 'propriedade' => 'outro_nome', );
     */
    protected $aliases = array();


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
        array $mapping = array(),
        array &$cache = array()
    ) {
        if (!empty($aliases)) {
            $this->aliases = $aliases;
        }

        if (!empty($mapping)) {
            $this->mapping = $mapping;
        }

        parent::__construct($data, $this->aliases, $this->mapping, $cache);
    }
}
