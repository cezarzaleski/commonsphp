<?php

namespace Commons\Country\Brazil\Validator;

class CpfValidator extends \Zend\Validator\AbstractValidator
{
    const INVALID = 'CPFInvalido';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => 'CPF inválido.',
    );

    protected $messageVariables = array(
        'cpf'  => 'value'
    );

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param mixed $value
     * @return boolean
     * @throws Exception\RuntimeException If validation of $value is impossible
     */
    public function isValid($value)
    {
        $this->setValue($value);

        $cpf = $this->trimCPF($value);
        if (!$this->respectsRegularExpression($cpf)) {
            $this->error(static::INVALID);
            return false;
        }

        if (!$this->applyingCpfRules($cpf)) {
            $this->error(static::INVALID);
            return false;
        }

        return true;
    }

    /**
     * @param $cpf
     * @return string
     */
    private function trimCPF($cpf)
    {
        return \trim(\preg_replace('/[.,-]/', '', $cpf));
    }

    /**
     * @param $cpf
     * @return bool
     */
    private function respectsRegularExpression($cpf)
    {
        $regularExpression = "[0-9]{3}\\.?[0-9]{3}\\.?[0-9]{3}-?[0-9]{2}";

        if (!@\preg_match("`^" . $regularExpression . "$`", $cpf)) {
            return false;
        }

        return true;
    }

    /**
     * @param $cpf
     * @return bool
     */
    private function applyingCpfRules($cpf)
    {
        $return = true;
        if (\strlen($cpf)!= 11 || \preg_match('`^([0-9])\\1{10}$`', $cpf)) {
            $return = false;
        } else {
            for ($verificador = 9; $verificador < 11; $verificador++) {
                $check = 0;
                for ($posicao = 0; $posicao < $verificador; $posicao++) {
                    $check += $cpf{$posicao} * (($verificador + 1) - $posicao);
                }

                $check = ((10 * $check) % 11) % 10;

                if ($cpf{$posicao} != $check) {
                    $return = false;
                }
            }
        }

        return $return;
    }
}
