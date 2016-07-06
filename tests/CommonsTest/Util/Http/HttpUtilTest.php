<?php

namespace CommonsTest\Util\Http;

use Commons\Util\Http\HttpUtil;

class HttpUtilTest extends \PHPUnit_Framework_TestCase
{
    public function testGetHeaders() {
        $_SERVER['HTTP_ACCEPT'] = 'Hello';
        $result = HttpUtil::getAllRequestHeaders();
        self::assertEquals("Hello", $result['Accept']);
    }
}
