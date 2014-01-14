<?php
namespace Panadas\Service\Session\Pdo;

class MySql extends \Panadas\Service\Session\PdoAbstract
{

    public function read($id)
    {
        $stmt = $this->getConnection()->prepare(
	        "
                SELECT `data`
                FROM `{$this->getTableName()}`
                WHERE
                    `id` = :id
                    AND `modified` > :modified
            "
        );

        $stmt->execute(
            [
                ":id" => $id,
                ":modified" => $this->getStaleModifiedDateTime()
            ]
        );

        if ($stmt->rowCount() === 0) {
            return "";
        }

        return base64_decode($stmt->fetchColumn());
    }

    public function write($id, $data)
    {
        $stmt = $this->getConnection()->prepare(
            "
                INSERT INTO `{$this->getTableName()}` (
                    `id`,
                    `data`,
                    `created`,
                    `modified`
                )
                VALUES (
                    :id,
                    :data,
                    :created,
                    `created`
                )
                ON DUPLICATE KEY UPDATE
                    `data` = VALUES(`data`),
                    `modified` = VALUES(`created`)
            "
        );

        $stmt->execute(
            [
                ":id" => $id,
                ":data" => base64_encode($data),
                ":created" => (new \DateTime())->format("Y-m-d H:i:s")
            ]
        );

        return true;
    }

    public function destroy($id)
    {
        $stmt = $this->getConnection()->prepare(
            "
                DELETE FROM `{$this->getTableName()}`
                WHERE `id` = :id
            "
        );

        $stmt->execute(
            [
                ":id" => $id
            ]
        );

        return true;
    }

    public function gc($lifetime)
    {
        $stmt = $this->getConnection()->prepare(
            "
                DELETE FROM `{$this->getTableName()}`
                WHERE `modified` <= :modified
            "
        );

        $stmt->execute(
            [
                ":modified" => $this->getStaleModifiedDateTime($lifetime)
            ]
        );

        return true;
    }

}
