<?php

namespace Test\Counters\Library;


class Application
{
    /**
     * Get/Set fields array
     * @var array
     */
    private $accessibleFields = [];

    public function __get($name)
    {
        if (array_key_exists($name, $this->accessibleFields)) {
            return $this->accessibleFields[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this->accessibleFields[$name] = $value;
    }

    /**
     * Application constructor.
     * @param array $config
     *
     * @return \Test\Counters\Library\Application
     */
    public function __construct($config)
    {
        $this->config = $config;

        if ($this->config['db']['is_enabled']) {
            $this->initDbConnection();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return 'application';
    }

    /**
     * Runs application request handling routine
     */
    public function run()
    {
        $router = new Router($this);
        $router->resolve();
    }

    /**
     * Inits DB connection
     */
    protected function initDbConnection()
    {
        if (
            array_key_exists('dsn', $this->config['db']) &&
            array_key_exists('user', $this->config['db']) &&
            array_key_exists('password', $this->config['db'])
        ) {
            $this->db = new \PDO($this->config['db']['dsn'], $this->config['db']['user'], $this->config['db']['password']);

            if ($this->config['application']['is_debug']) {
                $this->db->setAttribute( \PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING );
            }
        }

    }
}