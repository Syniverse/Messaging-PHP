#!/usr/bin/php
<?php

require 'vendor/autoload.php';

function check_state(string $mrqId, array $options)
{
    $session = new ScgApi\Session($options);
    $res = new ScgApi\MessageRequestResource($session);
    
    $mrq = $res->get($mrqId);
    print_r($mrq);

    foreach($res->listMessages($mrqId) as $m) 
    {
        print_r($m);
    }
}

if (sizeof($argv) < 3) {
    echo 'Usage: check_message_request_state.php auth-file request-id [api-url]' 
        . PHP_EOL;
    exit(-1);
}

$options['auth'] = $argv[1];

if (!empty($argv[3])) {
    $options['base_uri'] = $argv[3];
}

check_state($argv[2], $options);

?>