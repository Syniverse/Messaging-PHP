<?php

namespace ScgApi;

require 'vendor/autoload.php';

class ContactGroupResource extends ResourceBase
{
    public function __construct(Session $session)
    {
        parent::__construct($session, 
            'scg-external-api/api/v1/contact_groups');
    }

    public function listContacts(string $cgId, array $params = [])
    {
        $uri = $this->getUri($cgId);
        return $this->listPath($uri);
    }
    
    public function addContact(string $cgId, string $Contact)
    {
        return $this->addContacts($cgId, [$Contact]);
    }

    public function addContacts(string $cgId, array $contacts)
    {
        $uri = $this->getUri($cgId);
        $res = new NetResource($this->getSession(), new Resource($uri));
        return $res->create(['contacts'=>$contacts]);
    }

    public function deleteContact(string $cgId, $Contact)
    {
        $uri = $this->getUri($cgId);
        $res = new NetResource($this->getSession(), new Resource($uri));
        $res->delete($Contact);
    }

    private function getUri(string $cgId)
    {
        return $this->getResourceInstancePath($cgId) . "/contacts";
    }
}

?>
