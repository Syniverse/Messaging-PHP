<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class BridgeResourceTest extends Setup
{
    public function testCreate()
    {
        $res = new \ScgApi\BridgeResource(self::$session);

        $bridgeId = $res->create(['call_ids'=>[]])['id'];

        $this->assertTrue(!empty($bridgeId));
        return $bridgeId;
    }

    /**
     * @depends testCreate
     */
    public function testGet($bridgeId)
    {
        $res = new \ScgApi\BridgeResource(self::$session);
        $bridge = $res->get($bridgeId);
        $this->assertTrue($bridgeId == $bridge['id']);
        return $bridgeId;
    }

    /**
     * @depends testGet
     */
    public function testList($bridgeId)
    {
        $res = new \ScgApi\BridgeResource(self::$session);
        $pass = FALSE;
        foreach($res->list() as $i) {
            $pass = TRUE;
            break;
        }
        $this->assertTrue($pass);
        return $bridgeId;
    }

    /**
     * @depends testList
     */
    public function testDelete($bridgeId)
    {
        $res = new \ScgApi\BridgeResource(self::$session);
        $res->delete($bridgeId);
        $this->assertTrue(TRUE);
        return $bridgeId;
    }

    /**
     * @depends testDelete
     * @expectedException Exception
     */
    public function testIsDeleted($bridgeId)
    {
        $res = new \ScgApi\BridgeResource(self::$session);
        $res->get($bridgeId);
        $this->assertTrue(TRUE);
    }
}
?>