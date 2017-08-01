<?php

namespace ScgApi;

require 'vendor/autoload.php';

class MessageRequestResource extends ResourceBase
{
    private $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
        parent::__construct($session, 
            'scg-external-api/api/v1/messaging/message_requests');
    }

    public function listMessages(string $id, array $args = null)
    {
        return $this->listPath(
            $this->getResourceInstancePath($id) . '/messages',
            $args);
    }
}

?>