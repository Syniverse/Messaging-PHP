<?php

namespace ScgApi;

require 'vendor/autoload.php';

class SenderIdResource extends ResourceBase
{
    public function __construct(Session $session)
    {
        parent::__construct($session, 
            'scg-external-api/api/v1/messaging/sender_ids');
    }

    public function purchase(string $senderId, array $args)
    {
        $uri = $this->getResourceInstancePath($senderId) . "/purchase";
        $res = new NetResource($this->getSession(), new Resource($uri));
        return $res->create([$args]);
    }
}

?>