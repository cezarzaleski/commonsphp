<?php

namespace Commons\Util\Annotation;

use Commons\Exception\InvalidArgumentException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Doctrine\Common\Annotations\AnnotationRegistry;

class AnnotationUtil
{
    /**
     * Retorna a lista de anotações padrão que não devem ser processadas.
     * @see AnnotationRe
     * @return array
     */
    private static function getIgnoredAnnotationNames()
    {
        return array(
            // Annotation tags
            'Annotation' => true, 'Attribute' => true, 'Attributes' => true,
            /* Can we enable this? 'Enum' => true, */
            'Required' => true,
            'Target' => true,
            // Widely used tags (but not existent in phpdoc)
            'fix' => true , 'fixme' => true,
            'override' => true,
            // PHPDocumentor 1 tags
            'abstract'=> true, 'access'=> true,
            'code' => true,
            'deprec'=> true,
            'endcode' => true, 'exception'=> true,
            'final'=> true,
            'ingroup' => true, 'inheritdoc'=> true, 'inheritDoc'=> true,
            'magic' => true,
            'name'=> true,
            'toc' => true, 'tutorial'=> true,
            'private' => true,
            'static'=> true, 'staticvar'=> true, 'staticVar'=> true,
            'throw' => true,
            // PHPDocumentor 2 tags.
            'api' => true, 'author'=> true,
            'category'=> true, 'copyright'=> true,
            'deprecated'=> true,
            'example'=> true,
            'filesource'=> true,
            'global'=> true,
            'ignore'=> true, /* Can we enable this? 'index' => true, */ 'internal'=> true,
            'license'=> true, 'link'=> true,
            'method' => true,
            'package'=> true, 'param'=> true, 'property' => true, 'property-read' => true, 'property-write' => true,
            'return'=> true,
            'see'=> true, 'since'=> true, 'source' => true, 'subpackage'=> true,
            'throws'=> true, 'todo'=> true, 'TODO'=> true,
            'usedby'=> true, 'uses' => true,
            'var'=> true, 'version'=> true,
            // PHPUnit tags
            'codeCoverageIgnore' => true, 'codeCoverageIgnoreStart' => true, 'codeCoverageIgnoreEnd' => true,
            // PHPCheckStyle
            'SuppressWarnings' => true,
            // PHPStorm
            'noinspection' => true,
            // PEAR
            'package_version' => true,
            // PlantUML
            'startuml' => true, 'enduml' => true,
        );
    }

    /**
     * Carrega anotações a serem utilizadas no projeto através de seus namespaces e diretórios de residencia.
     *
     * @param string $namespace
     * @param string $dirs
     */
    public static function loadAnnotationsFromNamespace($namespace, $dirs)
    {
        AnnotationRegistry::registerAutoloadNamespace($namespace, $dirs);
    }

    /**
     * Método responsável por extrair as anotações de um Reflector.
     *
     * @param \Reflector $reflection Classe, função, método ou propriedade de onde se extrairá as anotações.
     * @param \Commons\Pattern\Cache\Cache $cache Cache para guardar as anotações extraídas.
     * @return array vazio ou com a lista de anotações.
     */
    public static function extractAnnotations(\Reflector $reflection, $cache = null)
    {
        // verifica cache
        $key = null;
        if ($cache !== null) {
            $aliasNamespace = __METHOD__;
            $name = (\method_exists($reflection, 'getName')) ? $reflection->getName() : (string)$reflection;
            $key = \sha1($aliasNamespace . $name);
            if ($cache->contains($key)) {
                return $cache->get($key);
            }
        }

        $annotations = array();
        if ($reflection instanceof \ReflectionFunction) {
            $doc = new DocParser();
            $doc->setIgnoredAnnotationNames(static::getIgnoredAnnotationNames());
            $annotations = $doc->parse($reflection->getDocComment());
        } else {
            $annotationReader = new AnnotationReader();
            if ($reflection instanceof \ReflectionClass) {
                $annotations = $annotationReader->getClassAnnotations($reflection);
            } elseif ($reflection instanceof \ReflectionMethod) {
                $annotations = $annotationReader->getMethodAnnotations($reflection);
            } elseif ($reflection instanceof \ReflectionProperty) {
                $annotations = $annotationReader->getPropertyAnnotations($reflection);
            }
        }

        // inclui no cache
        if ($cache !== null) {
            $cache->set($key, $annotations);
        }

        return $annotations;
    }

    /**
     * Encontra uma anotação.
     *
     * @param string $type Classe tipo a qual a anotação deve pertencer.
     * @param array $annotations Lista de anotações encontradas no comentário da função.
     * @param string $unique se true apenas pode haver uma única anotação, false caso contrário.
     * @throws InvalidArgumentException Se uma anotação única aparecer mais de uma vez na declaração da função.
     * @return mixed
     */
    public static function findAnnotation($type, array $annotations, $unique = false, $default = null)
    {
        $result = null;
        foreach ($annotations as $annotation) {
            $isSubClass = \is_a($annotation, $type);
            if (!$result) {
                if ($isSubClass) {
                    $result = $annotation;
                }
            } elseif ($isSubClass && $unique) {
                throw new InvalidArgumentException(
                    'Its not allowed to use more than one '.$type.' annotation type in a method or function.'
                    );
            }
        }
        if (!$result) {
            $result = $default;
        }
        return $result;
    }
}
