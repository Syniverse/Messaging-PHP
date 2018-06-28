#!/usr/bin/php
<?php

require 'vendor/autoload.php';

function send_sms(string $senderid, string $mdn, string $content, array $options)
{
    $session = new ScgApi\Session($options);
    $res = new ScgApi\MessageRequestResource($session);
    $request_id = $res->create(
        ['from' => "${senderid}", 
        'to' =>[$mdn], 
        'body' => $content
        ])['id'];

    echo "Created message request ${request_id}" . PHP_EOL;
}

if (sizeof($argv) < 5) {
    echo 'Usage: send_sms.php auth-file senderid receipient message [api-url]' . PHP_EOL;
    exit(-1);
}

$options['auth'] = $argv[1];

if (!empty($argv[5])) {
    $options['base_uri'] = $argv[5];
}

send_sms($argv[2], $argv[3], $argv[4], $options);

?>
