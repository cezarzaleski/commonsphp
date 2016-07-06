<?php

namespace Commons\Country\Brazil\Validator;

use Commons\Pattern\Validator\Validatable;

/**
 * Validador de telefones.
 * Baseado nas normas:
 *  http://legislacao.anatel.gov.br/resolucoes/2013/446-resolucao-607
 *  http://legislacao.anatel.gov.br/resolucoes/13-1998/336-resolucao-86
 *  http://legislacao.anatel.gov.br/resolucoes/25-2010/16-resolucao-553#art8anexo
 *  http://www.anatel.gov.br/Portal/verificaDocumentos/documento.asp?numeroPublicacao=318198
 *  &pub=principal&filtro=1&documentoPath=318198.pdf
 *
 * Referências das Regex construídas:
 * Regex SMC com migração com nono dígito (opcional até 2016 para outros estados ainda não convertidos):
 * ^(\+? ?(55|\(55\)) ?)
 * (?=(?P<ddd>(?!\(?(23|2[5-6]|29|36|39|52|5[6-9]|72|76|78)\)?)([1-9][1-9]|\([1-9][1-9]\))))
 * ((?P<novo_digito>(?=\(?(1[1-9]|2[1-2]|24|2[7-8]|9[1-9])\)?))([1-9][1-9]|\([1-9][1-9]\)) ?9|
 * ((?!(?P=novo_digito))([1-9][1-9]|\([1-9][1-9]\)) ?9?))
 * ([6-9][0-9]{3}-?[0-9]{4})$
 *
 * Regex SMC com migração com nono dígito (opcional até 2016 para outros estados ainda não convertidos),
 * considerando a inclusão de novos números quando o nono dígito for obrigatório (utilizada atualmente no código):
 * ^(\+? ?(55|\(55\)) ?)
 * (?=(?P<ddd>(?!\(?(23|2[5-6]|29|36|39|52|5[6-9]|72|76|78)\)?)([1-9][1-9]|\([1-9][1-9]\))))
 * ((?P<novo_digito>(?=\(?(1[1-9]|2[1-2]|24|2[7-8]|9[1-9])\)?))([1-9][1-9]|\([1-9][1-9]\)) ?9([0-9]{4}-?[0-9]{4})|
 * ((?!(?P=novo_digito))([1-9][1-9]|\([1-9][1-9]\)) ?9?)([6-9][0-9]{3}-?[0-9]{4}))$
 *
 * Regex SMC migrado (pós 2016)
 * ^(\+? ?(55|\(55\)) ?)
 * (?P<ddd>(?!\(?(23|2[5-6]|29|36|39|52|5[6-9]|72|76|78)\)?)([1-9][1-9]|\([1-9][1-9]\)))
 * 9([0-9]{4}-?[0-9]{4})$
 *
 * Regex STFC
 * ^(\+? ?(55|\(55\)) ?)
 * (?P<ddd>(?!\(?(23|2[5-6]|29|36|39|52|5[6-9]|72|76|78)\)?)([1-9][1-9]|\([1-9][1-9]\)))
 * ([2-5][0-9]{3}-?[0-9]{4})$
 *
 * --- Fragmentos ----
 * Código país Brasil (55)
 * (\+? ?(55|\(55\)) ?)
 *
 * Código DDD
 * (?P<ddd>(?!\(?(23|2[5-6]|29|36|39|52|5[6-9]|72|76|78)\)?)([1-9][1-9]|\([1-9][1-9]\)))
 *
 * Código DDD com regra do novo dígito para celular (atual 2014, com opcionalidade de dígito 9 para demais regiões.)
 * (?=(?P<ddd>(?!\(?(23|2[5-6]|29|36|39|52|5[6-9]|72|76|78)\)?)([1-9][1-9]|\([1-9][1-9]\))))
 * ((?P<novo_digito>(?=\(?(1[1-9]|2[1-2]|24|2[7-8]|9[1-9])\)?))([1-9][1-9]|\([1-9][1-9]\)) ?9|
 * ((?!(?P=novo_digito))([1-9][1-9]|\([1-9][1-9]\)) ?9?))
 *
 * Número de telefone celular(SMC - Sistema Móvel Celular):
 * ([6-9][0-9]{3}-?[0-9]{4})
 *
 * Número de telefone celular(SMC - Sistema Móvel Celular) FUTURO pós 2016:
 * 9([0-9]{4}-?[0-9]{4})
 *
 * Número de telefone fixo (STFC - Serviço de Telefonia Fixa Comutada):
 * ([2-5][0-9]{3}-?[0-9]{4})
 *
 * Número de telefone irrestrito (default)
 * ([0-9]{5}-?[0-9]{4})
 *
 * Uso:
 * Devem ser definidas as opções do construtor:
 * - telefone.is_mobile - true para se é celular, false, telefone fixo.
 * - telefone.regex.smc_model - Regex para o padrão SMC - Sistema Móvel Celular.
 * - telefone.regex.stfc_model - Regex para o padrão STFC - Serviço de Telefonia Fixa Comutada.
 *
 * @since 2014-12-17
 */
class TelefoneValidator extends \Zend\Validator\AbstractValidator implements Validatable
{
    const TELEFONE_INVALID = 'telefoneInvalido';
    const CELULAR_INVALID = 'celularInvalido';

    const CONFIG_IS_MOBILE = 'telefone.is_mobile';
    const CONFIG_REGEX_SMC_MODEL = 'telefone.regex.smc_model';
    const CONFIG_REGEX_STFC_MODEL = 'telefone.regex.stfc_model';

    public function __construct($options = null)
    {
        if ($options === null) {
            $options = array();
        }
        if (!isset($options[static::CONFIG_IS_MOBILE])) {
            $options[static::CONFIG_IS_MOBILE] = false;
        }
        if (!isset($options[static::CONFIG_REGEX_SMC_MODEL])) {
            $options[static::CONFIG_REGEX_SMC_MODEL] =
                // código do país
                '(\+? ?(55|\(55\)) ?)'.
                // configuração do ddd
                '(?=(?P<ddd>(?!\(?(23|2[5-6]|29|36|39|52|5[6-9]|72|76|78)\)?)([1-9][1-9]|\([1-9][1-9]\))))'.
                // configuração dos ddd com uso obrigatório do dígito 9
                '(((?=\(?(1[1-9]|2[1-2]|24|2[7-8]|9[1-9])\)?))([1-9][1-9]|\([1-9][1-9]\)) ?9([0-9]{4}-?[0-9]{4})|'.
                // configuração para os demais estados com dígito 9 opcional até o fim de 2016
                '((?!(?7))([1-9][1-9]|\([1-9][1-9]\)) ?9?)([6-9][0-9]{3}-?[0-9]{4}))';
        }
        if (!isset($options[static::CONFIG_REGEX_STFC_MODEL])) {
            $options[static::CONFIG_REGEX_STFC_MODEL] =
                // código do país
                '(\+? ?(55|\(55\)) ?)'.
                // configuração do ddd
                '(?P<ddd>(?!\(?(23|2[5-6]|29|36|39|52|5[6-9]|72|76|78)\)?)([1-9][1-9]|\([1-9][1-9]\)))'.
                // configuração dos números de telefonia fixa
                '([2-5][0-9]{3}-?[0-9]{4})';
        }
        parent::__construct($options);
    }

    protected $messageTemplates = array(
        self::CELULAR_INVALID =>
            'Celular inválido, verificar se o DDD é válido ou se o número não é de telefone fixo ou reservado.',
        self::TELEFONE_INVALID =>
            'Telefone inválido, verificar se o DDD é válido ou se o número não é de celular ou reservado.',
    );

    public function isValid($value)
    {
        $celular = $this->getOption(static::CONFIG_IS_MOBILE);
        $regularExpression = $this->createRegex($celular);

        if (!@\preg_match("`^" . $regularExpression . "$`", $value)) {
            if ($celular) {
                $this->error(static::CELULAR_INVALID);
            } else {
                $this->error(static::TELEFONE_INVALID);
            }
            return false;
        }
        return true;
    }

    private function createRegex($celular)
    {
        $model = null;
        if ($celular) {
            $model = $this->getOption(static::CONFIG_REGEX_SMC_MODEL);
        } else {
            $model = $this->getOption(static::CONFIG_REGEX_STFC_MODEL);
        }
        return $model;
    }
}
