<?php

namespace ScgApi;

require 'vendor/autoload.php';

class ResourceBase {
    public $resource;
    public $sample;
    public $name;
    private $path;
    private $session;

    public function __construct(Session $session, string $path)
    {
        $this->path = $path;
        $this->resource = new NetResource($session, new Resource($this->path));
        $this->session = $session;
    }

    public function getResourceInstancePath(string $id = null)
    {
        if (!empty($id)) {
            return $this->path . "/${id}";
        }
        return $this->path;
    }

    public function getSession()
    {
        return $this->session;
    }

    
    public function create(array $params = null){
        return $this->resource->create($params);
    }

    static public function doList(NetResource $res, array $args = null)
    {
        $offset = 0;
        if ($args === null) {
            $args = [];
        }

        do {
            $args['offset'] = $offset;
            $data = $res->list($args);
            $offset += count($data['list']);
            foreach($data['list'] as $d) {
                yield $d;
            }
        } while (count($data['list']) > 0);
    }

    // Returns a generator suited for 'foreach'
    public function listPath(string $path) {
        $res = new NetResource($this->session, new Resource($path));
        return $this->doList($res);
    }


    // Returns a generator suited for 'foreach'
    public function list(array $args=null)
    {
        return $this->doList($this->resource, $args);
    }

    // Returns a raw page of data.
    public function listRaw(array $args=null){
        return  $this->resource->list($args);
    }

    public function delete($id){
        return  $this->resource->delete($id);
    }

    public function get($id){
        return $this->resource->get($id);
    }

    public function update($id, $params){
        $res = $this->resource->update($id, $params);
    }

    public function replace($id, $params){
        $this->resource->replace($id, $params);
    }
}

?>
