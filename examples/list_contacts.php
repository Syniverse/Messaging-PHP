#!/usr/bin/php
<?php

require 'vendor/autoload.php';

function list_contacts(array $options)
{
    $session = new ScgApi\Session($options);
    $res =  new ScgApi\ContactResource($session);

    foreach($res->list() as $c) {
        echo "Contact ${c['id']} ${c['first_name']} mdn: ${c['primary_mdn']}" 
            . PHP_EOL;
    }
}

if (sizeof($argv) == 1) {
    echo 'Usage: list_contacts.php auth-file [api-url]' . PHP_EOL;
    exit(-1);
}

$options['auth'] = $argv[1];

if (!empty($argv[2])) {
    $options['base_uri'] = $argv[2];
}

list_contacts($options);

?>
