<?php

namespace ScgApi;

require 'vendor/autoload.php';

class ContactAddressHistoryResource extends ResourceBase
{
    public function __construct(Session $session)
    {
        parent::__construct($session, 
            'scg-external-api/api/v1/consent/contact_address_history');
    }
}

?>


