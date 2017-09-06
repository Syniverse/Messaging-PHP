<?php

namespace ScgApi;

require 'vendor/autoload.php';
use GuzzleHttp\Psr7\Request;


class Resource{

   private $uri;

    public function __construct($uri){
        $this->uri = $uri;
    }

    function create($body){
        return new Request ('POST', $this->uri,
                            ["content-type" => 'Application/json'],
                            json_encode($body));
    }

    public function list(array $args=null){
        $cnt = 0;
        $query = '';
        foreach($args as $key => $val) {
            if ($cnt++ == 0) {
                $query = "/?${key}=${val}";
            } else {
                $query = "${query}&${key}=${val}";
            }
        }
        return new Request ('GET', $this->uri . $query);
    }

    public function get($id,$query=null){
        return new Request ('GET', $this->uri . '/' . $id . $query);
    }

    public function delete($id){
        return new Request ('DELETE', $this->uri . '/' . $id);
    }

    public function update($id, $body){
        return new Request ('POST', $this->uri . '/' . $id, ["content-type" => 'Application/json'], json_encode($body));
    }

    public function replace($id, $body){
        return new Request ('PUT', $this->uri . '/' . $id, ["content-type" => 'Application/json'], json_encode($body));
    }
}



?>
