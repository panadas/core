<?php
namespace Panadas\Service\Authentication\Local;

abstract class PdoAbstract extends \Panadas\Service\Authentication\LocalAbstract
{

    private $connection;
    private $table_name;

    public function __construct(
        \Panadas\App $app,
        \PDO $connection,
        callable $user_callback,
        callable $user_id_callback,
        $header_name = "X-Auth-Token",
        $cookie_name = "authtoken",
        $cookie_path = "/",
        $cookie_domain = null,
        $lifetime = 10800,
        $persist_lifetime = null,
        $table_name = "authentication"
    )
    {
        parent::__construct(
            $app,
            $user_callback,
            $user_id_callback,
            $header_name,
            $cookie_name,
            $cookie_path,
            $cookie_domain,
            $lifetime,
            $persist_lifetime
        );

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

    public static function createConnection($dsn, $username, $password, $options = [])
    {
        $options[\PDO::ATTR_ERRMODE] = \PDO::ERRMODE_EXCEPTION;

        return new \PDO($dsn, $username, $password, $options);
    }

}
