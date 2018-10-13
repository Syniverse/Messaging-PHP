#!/usr/bin/php
<?php

require 'vendor/autoload.php';

function process(string $cmd, array $args) {
    $options = [];
    if (array_key_exists('auth', $args)) {
        $options['auth'] = $args['auth'];
    }

    if (array_key_exists('api', $args)) {
        $options['base_uri'] = $args['api'];
    }

    $session = new ScgApi\Session($options);
    $res =  new ScgApi\SenderIdResource($session);

    if ($cmd == 'create') {
        $id = $res->create([
            'name' => 'sender-wa-' . $args['phone-number'],
            'capabilities' => ['WHATSAPP'],
            'class_id' => 'COMMERCIAL',
            'type_id' => 'WHATSAPP',
            'address' => $args['phone-number'],
            'ownership' => 'PRIVATE',
            'credentials' => '{"token": "' . $args['token'] . '"}'
            ])['id'];

        echo "Created sender-id ${id}" . PHP_EOL;
    } else if ($cmd == 'init') {
        $res->init_whatsapp_registration($args['sender-id'],
            $args['register-method']);
    } else if ($cmd == 'activate') {
        $res->activate_whatsapp_registration($args['sender-id'],
            $args['verification-code']);
    } else if ($cmd == 'deactivate') {
        $res->deactivate($args['sender-id']);
    } else if ($cmd == 'reactivate') {
        $res->activate($args['sender-id']);
    }
}

// Make a key-value array of the command-line options
$my_args = array();
$cmd = '';
for ($i = 1; $i < count($argv); $i++) {
    if (preg_match('/^--([^=]+)=(.*)/', $argv[$i], $match)) {
        $my_args[$match[1]] = $match[2];
    } else {
        $cmd = $argv[$i];
    }
}

if (sizeof($my_args) == 0 || sizeof($argv) == 1) {
    echo 'Usage:' . PHP_EOL;
    echo $argv[0] . ': create --phone-number=MDN --token=TOKEN' . PHP_EOL;
    echo $argv[0] . ': init --sender-id=SENDER-ID --register-method=sms|voice' . PHP_EOL;
    echo $argv[0] . ': activate --sender-id=SENDER-ID --verification-code=CODE' . PHP_EOL;
    echo $argv[0] . ': deactivate --sender-id=SENDER-ID' . PHP_EOL;
    echo $argv[0] . ': reactivate --sender-id=SENDER-ID' . PHP_EOL;
    echo 'General options: [--auth=auth-file] [--api=api-url]' . PHP_EOL;
    exit(-1);
}

if (in_array($cmd, ['create', 'init', 'activate', 'deactivate', 'reactivate'])) {
    process($cmd, $my_args);
} else {
    echo 'Unknown command: ' . $cmd . PHP_EOL;
}

?>
