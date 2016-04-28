<?php

namespace Commons\Util\Serializer;

/**
 * Baseado em:
 * @link http://stackoverflow.com/questions/137021/php-object-as-xml-document
 * Implementado mecanismos para identificação de dependência cíclica.
 */
class XMLSerializer
{

    public static function generateValidXml($mixed, $blockName = 'component', $version = '1.0', $encoding = 'UTF-8')
    {
        return "<?xml version=\"$version\" encoding=\"$encoding\" ?>" .
            static::generateXmlStructure($mixed, $blockName);
    }

    public static function generateXmlStructure($mixed, $blockName = 'component')
    {
        return '<' . $blockName . '>' . static::generateXml($mixed) . '</' . $blockName . '>';
    }

    private static function generateXml($array, &$cache = array(), $counter = 0)
    {
        $xml = '';
        $nodeBlock = 'collection';
        $nodeName = 'item';
        if (\is_array($array) || \is_object($array)) {
            $name = '';
            if (\is_object($array)) {
                $name = \get_class($array);
                $array = \get_object_vars($array);
                $nodeBlock = 'object';
                $nodeName = 'property';
            } else {
                ++$counter;
                $name = \base64_encode('items_'.$counter);
            }

            $hash = \md5(\serialize($array));
            if (\array_key_exists($hash, $cache)) {
                $xml .= '<element ref="'. $cache[$hash] .'"/>';
            } else {
                $xml .= '<'. $nodeBlock .' name="' . $name . '">';
                foreach ($array as $key => $value) {
                    $xml .= '<' . $nodeName . ' name="'. $key .'">';
                    $xml .= static::generateXml($value, $cache, $counter);
                    $xml .= '</' . $nodeName . '>';
                }
                $xml .= '</'. $nodeBlock .'>';
                $cache[$hash] = $name;
            }
        } else {
            $xml = \htmlspecialchars($array, ENT_QUOTES);
        }

        return $xml;
    }
}
