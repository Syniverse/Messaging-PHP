#!/usr/bin/php
<?php

require 'vendor/autoload.php';

function send_mms(string $senderid, string $mdn, string $content,
                  string $attachment, array $options)
{
    $session = new ScgApi\Session($options);

    $att_res = new ScgApi\AttachmentResource($session);
    $att_id = $att_res->create(
        ['name' => 'test_upload', 'type' => 'image/jpeg',
        'filename' => 'cutecat.jpg'])['id'];

    echo "Created attachment ${att_id}. Will now upload." . PHP_EOL;

    // $attachment is the path to a file to upload
    $att_res->upload($att_id, $attachment);

    $mrq_res = new ScgApi\MessageRequestResource($session);

    $request_id = $mrq_res->create(
        ['from' => "sender_id:${senderid}",
        'to' =>[$mdn],
        'attachments' => [$att_id],
        'body' => $content])['id'];

    echo "Created message request ${request_id}" . PHP_EOL;
}

if (sizeof($argv) < 6) {
    echo 'Usage: send_mms.php auth-file senderid receipient message attachment [api-url]' . PHP_EOL;
    exit(-1);
}

$options['auth'] = $argv[1];

if (!empty($argv[6])) {
    $options['base_uri'] = $argv[6];
}

send_mms($argv[2], $argv[3], $argv[4], $argv[5], $options);

?>
