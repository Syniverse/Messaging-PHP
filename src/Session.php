<?php

namespace ScgApi;

require 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;


class Session{
    /* Session is a client that is responsible for sending requests to the
       server. Its responsibility is to handle authentication and session info */
    private $client;
    private $user_token;
    private $bearer_token;
    private $consumer_key;
    private $consumer_secret;
    private $config;
    public function __construct(array $conf = []){
        $this->config = array_merge([ // provide defaults
            'consumer_key' => 'not-specified-consumer-key',
            'consumer_secret' => 'not-specified-consumer-secret',
            'base_uri' =>'https://api.syniverse.com/',
            'user_token' =>'user_token',
            'bearer_token' =>'token',
            'timeout'=>30],$conf);

        if (!empty($conf['auth'])) {
            $this->config = Session::loadConfig($conf['auth'], $this->config);
        }

        $this->user_token = $this->config['user_token'];
        $this->bearer_token = $this->config['token'];
        $this->consumer_key = $this->config['consumer_key'];
        $this->consumer_secret = $this->config['consumer_secret'];
        $this->client = new Client($this->config);
    }

    public function refreshToken($auth_url='https://api.syniverse.com/saop-rest-data/v1/apptoken-refresh'){
        $res=new Resource($auth_url);
        $auth = $this->send($res->list("?consumerkey=" . $this->consumer_key .
                                       "&consumersecret=". $this->consumer_secret .
                                       "&oldtoken=" . $this->bearer_token));
        if($auth !=NULL){
            $this->bearer_token = $auth['accessToken'];
        }
    }

    public function send(Request $request){
        $r = $request ->withHeader(
            'Authorization', 'Bearer ' . $this->bearer_token);
        
        if (!empty($this->config['companyid'])) {
           $r = $r->withHeader('int-companyId', $this->config['companyid']);
        }

        if (!empty($this->config['appid'])) {
           $r = $r->withHeader('int-appId', $this->config['appid']);
        }

        if (!empty($this->config['transactionid'])) {
           $r = $r->withHeader('int-txnId', $this->config['transactionid']);
        }

        return json_decode($this->client->send($r)->getBody(),true);
    }

    static private function loadConfig(string $path, array $defaults)
    {
        $opts = json_decode(file_get_contents($path), true);
        return array_merge($defaults, $opts);
    }

    public function getBaseUri()
    {
        return $this->config['base_uri'];
    }

    public function getClient()
    {
        return $this->client;
    }
}

?>
