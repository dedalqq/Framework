<?php

namespace Framework;

use Framework\Exceptions\FatalException;
use Framework\MySQL\ActiveRecord;
use Framework\MySQL\Connection;

abstract class AbstractApp {

    private $project_folder = null;

    private $db_connection = null;

    /** @var AbstractRouter */
    protected $router = null;

    private $cur_time = null;

    /**
     * @var self
     */
    private static $instance = null;

    private function __construct() {

    }

    /**
     *
     */
    public function run() {
        try {
            $this->getRouter()->getController()->run();
        }
        catch (FatalException $exception) {
            var_dump($exception);
        }
    }

    /**
     * @return AbstractRouter
     */
    abstract protected function initRouter();

    /**
     * @return string
     */
    abstract protected function getSettingsFileName();

    /**
     * @return static
     */
    public static function i() {

        if (is_null(self::$instance)) {
            self::$instance = new static();
            self::$instance->init();
        }

        return self::$instance;
    }

    protected function init() {

        $this->cur_time = time();

        $settings_file_name = $this->getSettingsFileName();
        $settings = $this->getSettings($settings_file_name);

        $this->db_connection = new Connection($this);

        $db_settings = $settings->data_base;

        $this->db_connection->connect(
            $db_settings->host,
            $db_settings->login,
            $db_settings->password,
            $db_settings->db_name,
            $db_settings->port,
            $db_settings->table_prefix
        );

        ActiveRecord::setDbConnection($this->db_connection);

        $this->project_folder = $settings->project_folder;
    }

    public function time() {
        return $this->cur_time;
    }

    /**
     * @return Connection
     */
    public function getDBConnection() {
        return $this->db_connection;
    }

    /**
     * @return AbstractRouter
     */
    public function getRouter() {
        if (is_null($this->router)) {
            $this->router = $this->initRouter();
        }
        return $this->router;
    }

    public function getContents($file_name) {
        return file_get_contents($this->project_folder.'/'.$file_name);
    }

    /**
     * Возвращает настройки взятые из файла
     * @param $config_file
     * @return \stdClass
     */
    protected  function getSettings($config_file) {
        $content = file_get_contents($config_file);
        return json_decode($content);
    }

}