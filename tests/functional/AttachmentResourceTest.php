<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class AttachmentResourceTest extends Setup
{
    public function testCreate()
    {
        $res = new \ScgApi\AttachmentResource(self::$session);

        $attId = $res->create([
            'name' => 'test_upload',
            'type' => 'image/jpeg',
            'filename' => 'cutecat.jpg'
            ])['id'];

        $this->assertTrue(!empty($attId));
        return $attId;
    }

    /**
     * @depends testCreate
     */
    public function testGet($attId)
    {
        $res = new \ScgApi\AttachmentResource(self::$session);
        $att = $res->get($attId);
        $this->assertTrue($att['id'] == $attId);
        $this->assertTrue($att['type'] == 'image/jpeg');
        $this->assertTrue($att['name'] == 'test_upload');
        $this->assertTrue($att['filename'] == 'cutecat.jpg');
        return $attId;
    }

    /**
     * @depends testGet
     */
    public function testList($attId)
    {
        $res = new \ScgApi\AttachmentResource(self::$session);
        $pass = FALSE;
        foreach($res->list() as $att) {
            $pass = TRUE;
            break;
        }
        $this->assertTrue($pass);
        return $attId;
    }

    /**
     * @depends testList
     */
    public function testUpload($attId)
    {
        $res = new \ScgApi\AttachmentResource(self::$session);
        $content = 'This is test content';

        $tmpPath = tempnam(sys_get_temp_dir(), "scgatp-ft");
        $handle = fopen($tmpPath, "w");
        fwrite($handle, $content);
        fclose($handle);

        try {
            $res->upload($attId, $tmpPath);
            $att = $res->get($attId);
            $this->assertTrue($att['size'] == strlen($content));
        } finally {
            unlink($tmpPath);
        }

        return $attId;
    }

    /**
     * @depends testUpload
     */
    public function testDelete($attId)
    {
        $res = new \ScgApi\AttachmentResource(self::$session);
        $att = $res->delete($attId);
        $this->assertTrue(TRUE);
        return $attId;
    }

    /**
     * @depends testDelete
     * @expectedException Exception
     */
    public function testIsDeleted($attId)
    {
        $res = new \ScgApi\AttachmentResource(self::$session);
        $att = $res->get($attId);
        $this->assertTrue(TRUE);
    }

}

?>