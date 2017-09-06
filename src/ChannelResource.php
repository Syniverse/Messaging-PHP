<?php

namespace ScgApi;

require 'vendor/autoload.php';

class ChannelResource extends ResourceBase
{
    public function __construct(Session $session)
    {
        parent::__construct($session, 
            'scg-external-api/api/v1/messaging/channels');
    }

    public function listSenderIds(string $channelId, array $params = [])
    {
        $uri = $this->getUri($channelId);
        return $this->listPath($uri);
    }
    
    public function addSenderId(string $channelId, string $senderId)
    {
        return $this->addSenderIds($channelId, [$senderId]);
    }

    public function addSenderIds(string $channelId, array $senderIds)
    {
        $uri = $this->getUri($channelId);
        $res = new NetResource($this->getSession(), new Resource($uri));
        return $res->create(['sender_ids'=>$senderIds]);
    }

    public function deleteSenderId(string $channelId, $senderId)
    {
        $uri = $this->getUri($channelId);
        $res = new NetResource($this->getSession(), new Resource($uri));
        $res->delete($senderId);
    }

    private function getUri(string $channelId)
    {
        return $this->getResourceInstancePath($channelId) . "/sender_ids";
    }
}

?>
