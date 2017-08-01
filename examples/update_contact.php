#!/usr/bin/php
<?php

require 'vendor/autoload.php';

function update_contact(string $mdn, array $options)
{
    $session = new ScgApi\Session($options);
    $res =  new ScgApi\ContactResource($session);

    $contactId = $res->create([
        'first_name' =>'John',
        'last_name' => 'Doe',
        'primary_mdn' => $mdn
        ])['id'];

    $contact = $res->get($contactId);

    $res->update($contactId, [
        'last_name'=>'Anderson',
        'version_number' => $contact['version_number']
        ]);

    $contact = $res->get($contactId);

    echo "John Doe changed name to ${contact['first_name']} ${contact['last_name']}";
    echo PHP_EOL;
    $res->delete($contactId);
}

if (sizeof($argv) < 2) {
    echo 'Usage: update_contacts.php auth-file mdn [api-url]' . PHP_EOL;
    exit(-1);
}

$options['auth'] = $argv[1];

if (!empty($argv[3])) {
    $options['base_uri'] = $argv[3];
}

update_contact($argv[2], $options);

?>
