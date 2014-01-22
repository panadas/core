<?php
namespace Panadas\Service\Session;

abstract class PdoAbstract extends \Panadas\Service\SessionAbstract
{

    private $connection;
    private $table_name;

    public function __construct(
        \Panadas\App $app,
        \PDO $connection,
        $name = null,
        $lifetime = null,
        $cookie_path = "/",
        $cookie_domain = null,
        $table_name = "session"
    )
    {
        parent::__construct($app, $name, $lifetime, $cookie_path, $cookie_domain);

        $this
            ->setConnection($connection)
            ->setTableName($table_name);
    }

    public function getConnection()
    {
        return $this->connection;
    }

    protected function setConnection(\PDO $connection)
    {
        $this->connection = $connection;

        return $this;
    }

    public function getTableName()
    {
        return $this->table_name;
    }

    protected function setTableName($table_name)
    {
        $this->table_name = $table_name;

        return $this;
    }

    public function getStaleModifiedDateTime($lifetime = null)
    {
        if (is_null($lifetime)) {
            $lifetime = $this->getApp()->getServiceContainer()->get("session")->getLifetime();
        }

        return (new \DateTime("-{$lifetime} seconds"))->format("Y-m-d H:i:s");
    }

    public static function createConnection($dsn, $username, $password, $options = [])
    {
        $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;

        return new \PDO($dsn, $username, $password, $options);
    }

}
