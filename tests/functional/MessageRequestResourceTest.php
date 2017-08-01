<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class MessageRequestResourceTest extends Setup
{
    public function testCreate()
    {
        $res = new \ScgApi\MessageRequestResource(self::$session);

        $mrqId = $res->create([
            'from'=>'sender_id:'. self::$opts['senderIdSms'],
            'to'=>[(string)self::$opts['mdnRangeStart']],
            'body'=>'Hello world',
            'test_message_flag'=>true
        ])['id'];

        $this->assertTrue(!empty($mrqId));
        return $mrqId;
    }

    /**
     * @depends testCreate
     */
    public function testGet($mrqId)
    {
        $res = new \ScgApi\MessageRequestResource(self::$session);
        $mrq = $res->get($mrqId);
        $this->assertTrue($mrqId == $mrq['id']);
        return $mrqId;
    }

    /**
     * @depends testGet
     */
    public function testList($mrqId)
    {
        $res = new \ScgApi\MessageRequestResource(self::$session);
        $pass = FALSE;
        foreach($res->list() as $mrq) {
            $pass = TRUE;
            break;
        }
        $this->assertTrue($pass);
        return $mrqId;
    }

     /**
     * @depends testList
     */
    public function testListMessage($mrqId)
    {
        $res = new \ScgApi\MessageRequestResource(self::$session);
        $res->listMessages($mrqId);
        $this->assertTrue(TRUE);
        return $mrqId;
    }

    /**
     * @depends testListMessage
     */
    public function testDelete($mrqId)
    {
        $res = new \ScgApi\MessageRequestResource(self::$session);
        $res->delete($mrqId);
        $this->assertTrue(TRUE);
        return $mrqId;
    }

    /**
     * @depends testDelete
     * @expectedException Exception
     */
    public function testIsDeleted($mrqId)
    {
        $res = new \ScgApi\MessageRequestResource(self::$session);
        $res->get($mrqId);
        $this->assertTrue(TRUE);
    }
}
?>