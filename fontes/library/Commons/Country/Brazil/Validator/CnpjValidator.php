<?php

namespace Commons\Country\Brazil\Validator;

class CnpjValidator extends \Zend\Validator\AbstractValidator
{
    const INVALID = "CNPJInvalido.";

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $messageTemplates = array(
        self::INVALID => "CNPJ inválido.",
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
        $return = false;
        $cnpj = $this->trimCNPJ($value);
        if ($this->respectsRegularExpression($cnpj) != 1) {
            $this->error(static::INVALID);
            $return = false;
        } else {
            $x = strlen($cnpj) - 2;
            if ($this->applyingCnpjRules($cnpj, $x) == 1) {
                $x = strlen($cnpj) - 1;
                if ($this->applyingCnpjRules($cnpj, $x) == 1) {
                    $return = true;
                } else {
                    $this->error(static::INVALID);
                    $return = false;
                }
            } else {
                $this->error(static::INVALID);
                $return = false;
            }
        }
        return $return;
    }

    /**
     * @param $cnpj
     * @return string
     */
    private function trimCNPJ($cnpj)
    {
        return \preg_replace('/[.,\/,-]/', '', $cnpj);
    }

    /**
     * @param $cnpj
     * @return bool
     */
    private function respectsRegularExpression($cnpj)
    {
        $regularExpression = "[0-9]{2,3}\\.?[0-9]{3}\\.?[0-9]{3}/?[0-9]{4}-?[0-9]{2}";

        if (!@\preg_match("`^" . $regularExpression . "$`", $cnpj)) {
            return false;
        }

        return true;
    }

    /**
     * @param $cnpj
     * @param $x
     * @return bool
     */
    private function applyingCnpjRules($cnpj, $x)
    {
        $VerCNPJ = 0;
        $ind = 2;

        for ($y = $x; $y>0; $y--) {
            $VerCNPJ += (int) substr($cnpj, $y-1, 1) * $ind;
            if ($ind > 8) {
                $ind = 2;
            } else {
                $ind++;
            }
        }

        $VerCNPJ %= 11;
        if (($VerCNPJ == 0) || ($VerCNPJ == 1)) {
            $VerCNPJ = 0;
        } else {
            $VerCNPJ = 11 - $VerCNPJ;
        }

        return ($VerCNPJ == (int) substr($cnpj, $x, 1));
    }
}
