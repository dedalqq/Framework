<?php

namespace Framework\MySQL;

use Framework\AbstractApp;
use Framework\Exceptions\Fatal as FatalException;
use Framework\Exceptions\MySQL;

class Connection
{

    private $host = 'localhost';

    private $login = '';

    private $password = '';

    private $data_base = '';

    private $port = 3306;

    private $table_prefix = '';

    private $app = null;

    /**
     * @var \mysqli
     */
    private $connection = null;

    function __construct(AbstractApp $app)
    {
        $this->app = $app;
    }


    public function connect($host, $login, $password, $data_base, $port, $table_prefix)
    {
        $this->host = $host;
        $this->login = $login;
        $this->password = $password;
        $this->data_base = $data_base;
        $this->port = $port;
        $this->table_prefix = $table_prefix;

        $this->connection = mysqli_connect(
            $this->host,
            $this->login,
            $this->password,
            $this->data_base,
            $this->port
        );

    }

    public function setCharset($value = 'utf8') {
        $this->connection->set_charset($value);
    }

    public function isConnectTrue()
    {
        // @todo сделать по нормальному
        return $this->connection instanceof \mysqli;
    }

    public function setTablePrefix($prefix)
    {
        $this->table_prefix = $prefix;
        return $this;
    }

    /**
     * @return string
     */
    public function getTablePrefix()
    {
        return $this->table_prefix;
    }

    /**
     * @param $table_name
     * @return bool
     */
    public function dropTable($table_name)
    {
        $query = QueryBuilder::Drop($table_name);
        $result = $this->query($query->get());
        return $result->isSuccess();
    }


    public function getTableList()
    {
        $query = QueryBuilder::ShowTables();
        $result = $this->query($query);

        $data = array();

        while ($table_name = $result->fetch()) {
            $data[] = $table_name[0];
        }

        return $data;
    }

    public function escape($text)
    {
        return addslashes($text);
    }

    /**
     * @param QueryBuilder $query
     * @throws FatalException
     * @throws MySQL
     * @return Result|null
     */
    public function query(QueryBuilder $query)
    {
        $query->setTablePrefix($this->table_prefix);

        $result = $this->connection->query($query->get());

        if ($result === false) {
            throw new MySQL($this->connection->error);
        }

        if ($result === true) {
            return new Result(true, null);
        }

        if ($result instanceof \mysqli_result) {
            return new Result(true, $result);
        }

        throw new FatalException('MySQL server Error');
    }

    public function getLastId()
    {
        return $this->connection->insert_id;
    }

}