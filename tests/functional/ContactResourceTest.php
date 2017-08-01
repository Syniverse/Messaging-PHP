<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class ContactResourceTest extends Setup
{
    public function testCreate()
    {
        $res = new \ScgApi\ContactResource(self::$session);

        // Delete existing contact left over from flawed tests
        foreach($res->list(['primary_mdn' => self::$opts['mdnRangeStart']]) as $c)
        {
            $res->delete($c['id']);
        }

        $contactId = $res->create([
            'first_name'=>'ci-test', 
            'primary_mdn'=>(string)self::$opts['mdnRangeStart']
        ])['id'];

        $this->assertTrue(!empty($contactId));
        return $contactId;
    }

    /**
     * @depends testCreate
     */
    public function testGet($contactId)
    {
        $res = new \ScgApi\ContactResource(self::$session);
        $contact = $res->get($contactId);
        $this->assertTrue($contactId == $contact['id']);
        return $contactId;
    }

    /**
     * @depends testGet
     */
    public function testList($contactId)
    {
        $res = new \ScgApi\ContactResource(self::$session);
        $pass = FALSE;
        foreach($res->list() as $i) {
            $pass = TRUE;
            break;
        }
        $this->assertTrue($pass);
        return $contactId;
    }

    /**
     * @depends testList
     */
    public function testDelete($contactId)
    {
        $res = new \ScgApi\ContactResource(self::$session);
        $res->delete($contactId);
        $this->assertTrue(TRUE);
        return $contactId;
    }

    /**
     * @depends testDelete
     * @expectedException Exception
     */
    public function testIsDeleted($contactId)
    {
        $res = new \ScgApi\ContactResource(self::$session);
        $res->get($contactId);
        $this->assertTrue(TRUE);
    }
}
?>