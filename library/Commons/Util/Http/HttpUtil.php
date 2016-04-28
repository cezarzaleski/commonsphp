<?php
namespace Commons\Util\Http;

/**
 * Utilitários HTTP.
 */
class HttpUtil
{
    /**
     * Extrai do request todos os cabeçalhos HTTP.
     * @return string[]
     */
    public static function getAllRequestHeaders()
    {
        $headers = array();
        if (!\function_exists('getallheaders')) {
            foreach ($_SERVER as $name => $value) {
                if (\substr($name, 0, 5) == 'HTTP_') {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $headers[$key] = $value;
                }
            }
        } else {
            $headers = \getallheaders();
        }
        return $headers;
    }
}
