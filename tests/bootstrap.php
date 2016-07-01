<?php

ini_set('display_errors', '1');
ini_set('error_reporting', E_ALL);

$loader = include dirname(__DIR__) . '/vendor/autoload.php';

// Para carregar as anotações nos testes.
\Commons\Util\Annotation\AnnotationUtil::registerLoader(array($loader, 'loadClass'));

/* Zend Framework 1
ini_set('include_path', get_include_path() . PATH_SEPARATOR . realpath(__DIR__ . '/../library')); */
