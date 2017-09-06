<?php
/* Resources in the api are related one to another. SenderId resource
   has reference to senderId class, and senderId type. MessageRequest is related
   to a sender and so on. This information and current state is captured in
   simple Model. It is in general key/value store. */

namespace ScgApi;

require 'vendor/autoload.php';

class MessageTemplateResource extends ResourceBase
{
    public function __construct(Session $session)
    {
        parent::__construct($session, 
            'scg-external-api/api/v1/messaging/message_templates');
    }
}

?>