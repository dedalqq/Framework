<?php

namespace Framework;

use Framework\Exceptions\FatalException;
use Framework\Exceptions\Router;
use Framework\MySQL\ActiveRecord;
use Framework\MySQL\Connection;

abstract class AbstractApp {

    private $project_folder = null;

    private $db_connection = null;

    /** @var AbstractRouter */
    protected $router = null;

    private $cur_time = null;

    /** @var \stdClass */
    private $settings = null;

    private $project_domain = null;

    /**
     * @var self
     */
    private static $instance = null;

    private function __construct() {

    }

    protected function initApp() {

        return true;
    }

    public function getProjectDomain() {
        return $this->project_domain;
    }

    /**
     * @param $name
     * @return null|string
     */
    public function getCookieValue($name) {
        if (isset($_COOKIE[$name])) {
            return (string)$_COOKIE[$name];
        }
        return null;
    }

    /**
     *
     */
    public function run() {
        try {

            $this->initApp();

            $method = $this->getRouter()->getMethod();

            if (empty($method)) {
                throw new Router('Bad request!');
            }

            $controller = $this->getRouter()->getController();

            if (is_null($controller)) {
                throw new Router('Bad request!');
            }

            if ($controller->beforeAction()) {
                $controller->$method();
            }
            else {
                throw new Router('Bad request!');
            }
        }
        catch (FatalException $exception) {
            echo '<pre>';
            var_dump($exception);
            echo '</pre>';
        }
        catch (Router $exception) {
            echo '<pre>';
            var_dump($exception);
            echo '</pre>';
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

    private function init() {

        $this->cur_time = time();

        $settings = $this->getSettings();

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

        $this->project_folder = $settings->project_path;

        $this->project_domain = $settings->project_domain;
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
     * @return \stdClass
     */
    public function getSettings() {
        if (is_null($this->settings)) {
            $settings_file_name = $this->getSettingsFileName();
            $content = file_get_contents($settings_file_name);
            $this->settings = json_decode($content);
        }
        return $this->settings;
    }

}