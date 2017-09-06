<?php

namespace ScgApi;

require 'vendor/autoload.php';

class AttachmentResource extends ResourceBase
{
    public function __construct(Session $session)
    {
        parent::__construct($session, 
            'scg-external-api/api/v1/messaging/attachments');
    }

    public function createAccessToken(string $attachmentId)
    {
        $uri = $this->getResourceInstancePath($attachmentId) . '/access_tokens';
        $res = new NetResource($this->getSession(), new Resource($uri));
        return $res->create(null)['id'];
    }

    public function transferContent(string $token, $payload,
        string $type = 'application/octet-stream')
    {
        $session = $this->getSession();
        $uri = $session->getBaseUri()
            . "/scg-attachment/api/v1/messaging/attachments/${token}/content";

        $session->getClient()->request('POST', $uri,
            ['body' => $payload,
            'headers' => ['Content-Type' => $type]]);
    }

    public function upload(string $attachmentId, string $path)
    {
        $token = $this->createAccessToken($attachmentId);
        $this->transferContent($token, fopen($path, 'r'));
    }
}

?>
