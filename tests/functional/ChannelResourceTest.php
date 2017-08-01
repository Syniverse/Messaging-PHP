<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class ChannelResourceTest extends Setup
{
    public function testCreate()
    {
        $res = new \ScgApi\ChannelResource(self::$session);

        $channelId = $res->create([
            'name' => 'ci-test'
        ])['id'];

        $this->assertTrue(!empty($channelId));
        return $channelId;
    }

    /**
     * @depends testCreate
     */

    public function testAddSids(string $channelId) 
    {
        $res = new \ScgApi\ChannelResource(self::$session);
        $senderId = self::$opts['senderIdSms'];
        $res->addSenderIds($channelId, [$senderId]);
        $this->assertTrue(TRUE);
        return $channelId;
    }

    /**
     * @depends testAddSids
     */

    public function testListSid(string $channelId) 
    {
        $res = new \ScgApi\ChannelResource(self::$session);
        $gen = $res->listSenderIds($channelId);
        $list =  iterator_to_array($gen);
        $this->assertTrue(count($list) == 1);
        return $channelId;
    }

    /**
     * @depends testListSid
     */
    public function testDeleteSid(string $channelId)
    {
        $res = new \ScgApi\ChannelResource(self::$session);
        $res->deleteSenderId($channelId, self::$opts['senderIdSms']);
        $this->assertTrue(TRUE);
        return $channelId;
    }

    /**
     * @depends testDeleteSid
     */
    public function testAddSid(string $channelId)
    {
        $res = new \ScgApi\ChannelResource(self::$session);
        $senderId = self::$opts['senderIdSms'];
        $res->addSenderId($channelId, $senderId);
        $res->deleteSenderId($channelId, $senderId);
        $this->assertTrue(TRUE);
        return $channelId;
    } 

    /**
     * @depends testAddSid
     */
    public function testDeleteChannel(string $channelId)
    {
        $res = new \ScgApi\ChannelResource(self::$session);
        $res->delete($channelId);
        $this->assertTrue(TRUE);
        return $channelId;
    }

    /**
     * @depends testDeleteChannel
     * @expectedException Exception
     */
    public function testChannelIsDeleted(string $channelId)
    {
        $res = new \ScgApi\ChannelResource(self::$session);
        $res->get($channelId);
        $this->assertTrue(TRUE);
    }   
}
?>