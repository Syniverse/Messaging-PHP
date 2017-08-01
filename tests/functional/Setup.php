<?php

namespace ScgApi\Test\Functional;

require './vendor/autoload.php';

use PHPUnit\Framework\TestCase;

class Setup extends TestCase
{
    protected static $session;
    protected static $opts;

    public static function setUpBeforeClass() 
    {
        self::$opts = json_decode(
            file_get_contents(getenv('SCGAPI_SETUP')), true);
            
        $options = [
            'base_uri' => self::$opts['url'],
            'auth' => getenv('SCGAPI_AUTH')
        ];

        self::$session = new \ScgApi\Session($options);
    }

    public static function tearDownAfterClass()
    {
        self::$opts = null;
        self::$session = null; 
    }
}

?>