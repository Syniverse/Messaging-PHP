# PHP SDK for SCG Messaging APIs

This is the PHP version of the SCG API. 
The SCG APIs provides access to communication channels using SMS, MMS, 
Push Notification, OTT messaging and Voice. 

We have prepared a thin PHP wrapper over the REST API for
these services. 

The PHP SDK hides some of the REST API's constraints, like
lists being returned in logical pages of _n_ records. With the
PHP SDK, the list method returns a generator that works with *foreach()*.

Please register for a free account at https://developer.syniverse.com to get your API keys.

## External dependencies
Dependencies can be installed with [composer](https://getcomposer.org/).

- [guzzle](https://github.com/guzzle/guzzle)

To install the dependencies for ths project:
```sh
$ composer install
$ composer dump-autoload
```

## How to use the SDK
The PHP SDK implements a thin wrapper classes over the 
different Messaging API resources. Using these resource
classes, you can create, get, update, list, replace and delete
objects.

# Some examples

## List all contacts
```php
function list_contacts(array $options)
{
    $session = new ScgApi\Session($options);
    $res =  new ScgApi\ContactResource($session);

    foreach($res->list() as $c) {
        echo "Contact ${c['id']} ${c['first_name']} mdn: ${c['primary_mdn']}" 
            . PHP_EOL;
    }
}
```
This program will output something like:
```
Contact 4AOXpg8dlXRCMXlWDUch73 Alice mdn: 155560000002
Contact vsXgkqhUW4eIwMColyevn7 Bob mdn: 1555898230057
```
[Full example](examples/list_contacts.php)


## Create and update a contact

```php
function update_contact(string $mdn, array $options)
{
    $session = new ScgApi\Session($options);
    $res =  new ScgApi\ContactResource($session);

    $contactId = $res->create([
        'first_name' =>'John',
        'last_name' => 'Doe',
        'primary_mdn' => $mdn
        ])['id'];

    $contact = $res->get($contactId);

    $res->update($contactId, [
        'last_name'=>'Anderson',
        'version_number' => $contact['version_number']
        ]);

    $contact = $res->get($contactId);

    echo "John Doe changed name to ${contact['first_name']} ${contact['last_name']}";
    echo PHP_EOL;
}

```
You can create an object by calling *create* with a associate array
of the data values for the object you create.

In this example, please notice the *version_number* field that we copy from
the received data to the *update* argument. The server use optimistic
locking to safeguard against inconsistent updates of the data. In case
someone else updated the contact in the time window between your *get*
and *update* calls, the update will fail. In that case you must retry the
operation - first getting the data, with the new version, and then 
retrying the update with your changes - and the new *version_number*.
This pattern applies for all objects that can be updated.

The example will output:
```
John Doe changed name to John Anderson
```
[Full example](examples/update_contact.php)

## Error handling
Errors are reported trough exceptions. Please see the 
PHP [composer](https://getcomposer.org/) library
for reference.

# Some more examples

## Sending a SMS to a Mobile number

```php
function send_sms(string $senderid, string $mdn, string $content, array $options)
{
   $session = new ScgApi\Session($options);
    $res = new ScgApi\MessageRequestResource($session);
    $request_id = $res->create(
        ['from' => "sender_id:${senderid}", 
        'to' =>[$mdn], 
        'body' => $content
        ])['id'];

    echo "Created message request ${request_id}" . PHP_EOL;
}
```

[Full example](examples/send_sms.php)

## Sending a Message to a Contact

This works as above, except for the *to* field in *create*.
```php
    $request_id = $res->create(
        ['from' => "sender_id:${senderid}", 
        'to' =>['contact:' + contact_id], 
        'body' => $content
        ])['id'];

```

## Sending a Message to a Contact Group

Here we will create two new contacts, a new group, assign the contacts
to the group, and then send a message to the group.

```php
function send_sms(string $senderid, string $mdn1, string $mdn2, string $content, 
    array $options)
{
    $session = new ScgApi\Session($options);

    // Create a group
    $groupRes = new \ScgApi\ContactGroupResource($session);
    $friendsId = $groupRes->create(['name' => 'friends'])['id'];

    // Create contacts
    $contactRes = new \ScgApi\ContactResource($session);
    $alice = $contactRes->Create([
            'first_name'=>'Alice', 
            'primary_mdn'=>$mdn1
            ])['id'];
    $bob = $contactRes->Create([
            'first_name'=>'Bob', 
            'primary_mdn'=>$mdn2
            ])['id'];

    // Add the contacts to our group
    $groupRes->addContacts($friendsId, [$alice, $bob]);

    // Send an sms to our new friends
    $mrqRes = new ScgApi\MessageRequestResource($session);
    $requestId = $mrqRes->create([
        'from' => "sender_id:${senderid}",
        'to' => ["group:${friendsId}"],
        'body' => $content])['id'];

    echo "Created message request ${requestId}" . PHP_EOL;
}

```
[Full example](examples/send_sms_to_grp.php)

## Sending a MMS with an attachment

```php
function send_mms(string $senderid, string $mdn, string $content,
                  string $attachment, array $options)
{
    $session = new ScgApi\Session($options);

    $att_res = new ScgApi\AttachmentResource($session);
    $att_id = $att_res->create(
        ['name' => 'test_upload', 'type' => 'image/jpeg',
        'filename' => 'cutecat.jpg'])['id'];

    echo "Created attachment ${att_id}. Will now upload." . PHP_EOL;

    // $attachment is the path to a file to upload
    $att_res->upload($att_id, $attachment);

    $mrq_res = new ScgApi\MessageRequestResource($session);

    $request_id = $mrq_res->create(
        ['from' => "sender_id:${senderid}",
        'to' =>[$mdn],
        'attachments' => [$att_id],
        'body' => $content])['id'];

    echo "Created message request ${request_id}" . PHP_EOL;
}
```

[Full example](examples/send_mms.rb)

## Checking the state of a Message Request

```php
function check_state(string $mrqId, array $options)
{
    $session = new ScgApi\Session($options);
    $res = new ScgApi\MessageRequestResource($session);
    
    $mrq = $res->get($mrqId);
    print_r($mrq);

    foreach($res->listMessages($mrqId) as $m) 
    {
        print_r($m);
    }
}
```

The example below may output something like:
```
Array
(
    [application_id] => 888
    [company_id] => 12121
    [created_date] => 1501586862472
    [last_updated_date] => 1501586864444
    [version_number] => 2
    [id] => ryfKsD5F5z3VBE2iCs5C8
    [from] => sender_id:OgX4Y8AuTzO8VExVGphKkg
    [to] => Array
        (
            [0] => +155512345678
        )

    [body] => Hello world
    [state] => COMPLETED
    [recipient_count] => 1
    [sent_count] => 0
    [delivered_count] => 0
    [media_requested_count] => 0
    [read_count] => 0
    [click_thru_count] => 0
    [converted_count] => 0
    [canceled_count] => 0
    [failed_count] => 0
    [sender_id_sort_criteria] => Array
        (
        )

    [contact_delivery_address_priority] => Array
        (
        )

)
Array
(
    [application_id] => 888
    [company_id] => 12121
    [created_date] => 1501586864248
    [last_updated_date] => 1501586866275
    [version_number] => 2
    [id] => jx6bQRK2jEJ0emtj3B0Yz3
    [message_request_id] => ryfKsD5F5z3VBE2iCs5C8
    [direction] => MT
    [customer_sender_id] => OgX4Y8AuTzO8VExVGphKkg
    [from_address] => 2341026195
    [to_address] => +3598957000514
    [state] => SENT
    [body] => Hello world
    [sent_date] => 1501586864543
    [type] => SMS
    [destination_country] => BGR
    [price] => 0.0212
    [sender_id_alias] => OgX4Y8AuTzO8VExVGphKkg
    [fragments_info] => Array
        (
            [0] => Array
                (
                    [fragment_id] => 6KMxtsP0ndjgWT5AFxPH61
                    [fragment_state] => SENT
                    [charge] => 0.0212
                    [external_id] => 5150092268783864
                    [delivery_report_reference] => 6KMxtsP0ndjgWT5AFxPH61
                )

        )

    [consent_requirement] => NONE
)

```

[Full example](examples/check_message_request_state.php)

