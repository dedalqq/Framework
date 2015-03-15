<?php

namespace Framework\Mongo;

class Connection {

    private $client = null;

    private $db_name = '';

    public function __construct($host, $db_name, $port = 27017)
    {
        $connect_string = "mongodb://{$host}:{$port}";
        $this->db_name = $db_name;
        $this->client = new \MongoClient($connect_string);
    }

    /**
     * @return \MongoDB
     */
    public function getDataBase() {
        return $this->client->selectDB($this->db_name);
    }
}