#!/usr/bin/php
<?php

require 'vendor/autoload.php';

function get_id_from_mdn(ScgApi\ContactResource $res, string $mdn)
{
    foreach($res->list(['primary_mdn'=>$mdn]) as $contact)
    {
        // There will be at max one contact with any number.
        return $contact['id'];
    }

    throw new Exception("Could not find any contact with number $mdn");
}

function delete_contact(string $mdn, array $options)
{
    $session = new ScgApi\Session($options);
    $res =  new ScgApi\ContactResource($session);

    $id = get_id_from_mdn($res, $mdn);
    $res->delete($id);

    echo "Deleted contact $id with mdn $mdn" . PHP_EOL;
}

if (sizeof($argv) < 2) {
    echo 'Usage: delete_
contacts.php auth-file mdn [api-url]' . PHP_EOL;
    exit(-1);
}

$options['auth'] = $argv[1];

if (!empty($argv[3])) {
    $options['base_uri'] = $argv[3];
}

delete_contact($argv[2], $options);

?>