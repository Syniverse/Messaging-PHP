<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class SenderIdTypeResourceTest extends Setup
{
    public function testList()
    {
        $res = new \ScgApi\SenderIdTypeResource(self::$session);
        foreach($res->list() as $i) {
            $this->assertTrue(TRUE);
            return;          
        }
        $this->assertTrue(FALSE);
    }
}
?>