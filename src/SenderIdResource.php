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

    public function activate(string $senderId)
    {
        $this->set_state($senderId, 'ACTIVE', []);
    }

    public function deactivate(string $senderId)
    {
        $this->set_state($senderId, 'INACTIVE', []);
    }

    public function init_whatsapp_registration(string $senderId, string $registerMethod)
    {
        $this->set_state($senderId, "PENDING_CONFIRMATION", ['register_method' => $registerMethod]);
    }

    public function activate_whatsapp_registration(string $senderId, string $verificationCode)
    {
        $this->set_state($senderId, 'ACTIVE', ['verification_code' => $verificationCode]);
    }

    public function set_state(string $senderId, string $state,  array $args) {
        $sender_id = $this->get($senderId);

        $use_args = array_merge(['state'=>$state,'version_number' => $sender_id['version_number']], $args);
        $this->update($senderId, $use_args);
    }
}

?>
