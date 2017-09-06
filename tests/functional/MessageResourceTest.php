<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class MessageResourceTest extends Setup
{

    protected static $attachmentId;
    protected static $messageRequestId;

    public static function setUpBeforeClass() 
    {
        parent::setUpBeforeClass();

        // Upload an attachment
        $resAtt = new \ScgApi\AttachmentResource(self::$session);
        $attId = $resAtt->create([
            'name' => 'test_upload',
            'type' => 'image/jpeg',
            'filename' => 'cutecat.jpg'
            ])['id'];

        $content = 'This is test content';

        $tmpPath = tempnam(sys_get_temp_dir(), "scgatp-ft");
        $handle = fopen($tmpPath, "w");
        fwrite($handle, $content);
        fclose($handle);
        try {
            $resAtt->upload($attId, $tmpPath);
        } finally {
            unlink($tmpPath);
        }
        
        self::$attachmentId = $attId;
    }

    public static function tearDownAfterClass()
    {
        if (!empty(self::$attachmentId))
        {
            $res = new \ScgApi\AttachmentResource(self::$session);
            $res->delete(self::$attachmentId);
        }

        if (!empty(self::$messageRequestId))
        {
            $res = new \ScgApi\MessageRequestResource(self::$session);
            $res->delete(self::$messageRequestId);
        }

        parent::tearDownAfterClass();
    }

    public function testCreate()
    {
        // Create MMS
        $res = new \ScgApi\MessageRequestResource(self::$session);

        self::$messageRequestId = $res->create([
            'from'=>'sender_id:'. self::$opts['senderIdSms'],
            'to'=>[(string)self::$opts['mdnRangeStart']],
            'body'=>'Hello world',
            'attachments' => [self::$attachmentId],
            'test_message_flag'=>true
        ])['id'];

        $this->assertTrue(!empty(self::$messageRequestId));

        // Wait for a message to be available
        for($retry = 0; $retry < 30; $retry++)
        {
            foreach($res->listMessages(self::$messageRequestId) as $m) {
                // Pass the message-ID to the next test
                return $m['id'];
            }
            sleep(1);
        }

        throw new Exception('Timed out waiting for message to be created');
    }

    /**
     * @depends testCreate
     */
    public function testGet($messageId)
    {
        $res = new \ScgApi\MessageResource(self::$session);
        $message = $res->get($messageId);
        $this->assertTrue($messageId == $message['id']);
        return $messageId;
    }

    /**
     * @depends testGet
     */
    public function testList($messageId)
    {
        $res = new \ScgApi\MessageResource(self::$session);
        $pass = FALSE;
        foreach($res->list() as $message) {
            $pass = TRUE;
            break;
        }
        $this->assertTrue($pass);
        return $messageId;
    }

     /**
     * @depends testList
     */
    public function testListAttachments($messageId)
    {
        $res = new \ScgApi\MessageResource(self::$session);
        $res->listAttachments($messageId);
        $this->assertTrue(TRUE);
        return $messageId;
    }

    /**
     * @depends testListAttachments
     */
    public function testDelete($messageId)
    {
        $res = new \ScgApi\MessageResource(self::$session);
        $res->delete($messageId);
        $this->assertTrue(TRUE);
        return $messageId;
    }

    /**
     * @depends testDelete
     * @expectedException Exception
     */
    public function testIsDeleted($messageId)
    {
        $res = new \ScgApi\MessageResource(self::$session);
        $res->get($messageId);
        $this->assertTrue(TRUE);
    }
}
?>