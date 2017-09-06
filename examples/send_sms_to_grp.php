#!/usr/bin/php
<?php

require 'vendor/autoload.php';

function send_sms(string $senderid, string $mdn1, string $mdn2, string $content, 
    array $options)
{
    $session = new ScgApi\Session($options);

    // Create a group
    $groupRes = new \ScgApi\ContactGroupResource($session);
    $friendsId = $groupRes->create(['name' => 'friends'])['id'];

    // Create contacts
    $contactRes = new \ScgApi\ContactResource($session);
    $alice = $contactRes->Create([
            'first_name'=>'Alice', 
            'primary_mdn'=>$mdn1
            ])['id'];
    $bob = $contactRes->Create([
            'first_name'=>'Bob', 
            'primary_mdn'=>$mdn2
            ])['id'];

    // Add the contacts to our group
    $groupRes->addContacts($friendsId, [$alice, $bob]);

    // Send an sms to our new friends
    $mrqRes = new ScgApi\MessageRequestResource($session);
    $requestId = $mrqRes->create([
        'from' => "sender_id:${senderid}",
        'to' => ["group:${friendsId}"],
        'body' => $content])['id'];

    echo "Created message request ${requestId}" . PHP_EOL;

    // Clean up after this example. 
    $mrqRes->delete($requestId);
    $groupRes->delete($friendsId);
    $contactRes->delete($alice);
    $contactRes->delete($bob);
}

if (sizeof($argv) < 5) {
    echo 'Usage: send_sms.php auth-file senderid receipient1 receipient2 message [api-url]' . PHP_EOL;
    exit(-1);
}

$options['auth'] = $argv[1];

if (!empty($argv[6])) {
    $options['base_uri'] = $argv[6];
}

send_sms($argv[2], $argv[3], $argv[4], $argv[5], $options);

?>
