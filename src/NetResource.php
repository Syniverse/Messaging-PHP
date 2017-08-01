<?php

namespace ScgApi;

require 'vendor/autoload.php';
use GuzzleHttp\Psr7\Request;

class NetResource{
    private $client;
    private $resource;
    public function __construct($client, $res){
        $this->client = $client;
        $this->resource=$res;
    }

    public function __call( $name, $arguments )
    {
        if ( in_array( $name, ["create","list","get","delete","update","replace"]) )
        {
            return $this->client->send(call_user_func_array([$this->resource, $name], $arguments ));
        } else {
            trigger_error('Call to undefined method '.__CLASS__.'::'.$name.'()', E_USER_ERROR);
        }
    }
}


?>
