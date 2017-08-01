<?php

namespace ScgApi;

require 'vendor/autoload.php';

class BridgeResource extends ResourceBase
{
    public function __construct(Session $session)
    {
        parent::__construct($session, 
            'scg-external-api/api/v1/calling/bridges');
    }
}

?>
