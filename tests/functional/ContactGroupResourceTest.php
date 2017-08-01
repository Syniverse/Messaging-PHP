<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class ContactGroupResourceTest extends Setup
{
    protected static $contactId;

    public static function setUpBeforeClass() 
    {
        parent::setUpBeforeClass();

        $res = new \ScgApi\ContactResource(self::$session);

        // Delete existing contact left over from flawed tests
        foreach($res->list(['primary_mdn' => self::$opts['mdnRangeStart']]) as $c)
        {
            $res->delete($c['id']);
        }

        self::$contactId = $res->Create([
            'first_name'=>'ci-test', 
            'primary_mdn'=>(string)self::$opts['mdnRangeStart']
            ])['id'];
    }

    public  static function tearDownAfterClass()
    {
        $res = new \ScgApi\ContactResource(self::$session);
        $res->delete(self::$contactId);

        parent::tearDownAfterClass();
    }

    public function testCreate()
    {
        $res = new \ScgApi\ContactGroupResource(self::$session);

        $cgId = $res->create([
            'name' => 'ci-test'
        ])['id'];

        $this->assertTrue(!empty($cgId));
        return $cgId;
    }

    /**
     * @depends testCreate
     */

    public function testAddCids(string $cgId) 
    {
        $res = new \ScgApi\ContactGroupResource(self::$session);
        $res->addContacts($cgId, [self::$contactId]);
        $this->assertTrue(TRUE);
        return $cgId;
    }

    /**
     * @depends testAddCids
     */

    public function testListCid(string $cgId) 
    {
        $res = new \ScgApi\ContactGroupResource(self::$session);
        $gen = $res->listContacts($cgId);
        $list =  iterator_to_array($gen);
        $this->assertTrue(count($list) == 1);
        return $cgId;
    }

    /**
     * @depends testListCid
     */
    public function testDeleteCid(string $cgId)
    {
        $res = new \ScgApi\ContactGroupResource(self::$session);
        $res->deleteContact($cgId, self::$contactId);
        $this->assertTrue(TRUE);
        return $cgId;
    }

    /**
     * @depends testDeleteCid
     */
    public function testAddCid(string $cgId)
    {
        $res = new \ScgApi\ContactGroupResource(self::$session);
        $res->addContact($cgId, self::$contactId);
        $res->deleteContact($cgId, self::$contactId);
        $this->assertTrue(TRUE);
        return $cgId;
    } 

    /**
     * @depends testAddCid
     */
    public function testDeleteContactGroup(string $cgId)
    {
        $res = new \ScgApi\ContactGroupResource(self::$session);
        $res->delete($cgId);
        $this->assertTrue(TRUE);
        return $cgId;
    }

    /**
     * @depends testDeleteContactGroup
     * @expectedException Exception
     */
    public function testContactGroupIsDeleted(string $cgId)
    {
        $res = new \ScgApi\ContactGroupResource(self::$session);
        $res->get($cgId);
        $this->assertTrue(TRUE);
    }   
}
?>