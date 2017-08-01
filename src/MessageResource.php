<?php

namespace ScgApi;

require 'vendor/autoload.php';

class MessageResource extends ResourceBase
{
    public function __construct(Session $session)
    {
        parent::__construct($session, 
            'scg-external-api/api/v1/messaging/messages');
    }

    public function listAttachments(string $messageId, array $args = null)
    {
        return $this->listPath(
            $this->getResourceInstancePath($messageId) . '/attachments',
            $args);
    }
}

?>
