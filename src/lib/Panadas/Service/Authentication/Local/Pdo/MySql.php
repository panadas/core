<?php
namespace Panadas\Service\Authentication\Local\Pdo;

class MySql extends \Panadas\Service\Authentication\Local\PdoAbstract
{

    protected function create($user_id, $lifetime)
    {
        $db = $this->getConnection();

        $stmt = $db->prepare(
	        "
                SELECT COUNT(`token`)
                FROM `{$this->getTableName()}`
                WHERE `token` = :token
            "
        );

        $unique = false;

        while (!$unique) {

            $token = $this->generateToken();

            $stmt->execute(
	            [
	                ":token" => $token
                ]
            );

            $unique = ($stmt->fetchColumn() == 0);

        }

        $stmt = $db->prepare(
	        "
                INSERT INTO `{$this->getTableName()}` (
                    `token`,
                    `user_id`,
                    `lifetime`,
                    `created`,
                    `modified`
                ) VALUES (
                    :token,
                    :user_id,
                    :lifetime,
                    :created,
                    `created`
                );
            "
        );

        $stmt->bindValue(":token", $token);
        $stmt->bindValue(":user_id", $user_id, $db::PARAM_INT);

        if (is_null($lifetime)) {
            $stmt->bindValue(":lifetime", null, $db::PARAM_NULL);
        } else {
            $stmt->bindValue(":lifetime", $lifetime, $db::PARAM_INT);
        }

        $stmt->bindValue(":created", (new \DateTime())->format("Y-m-d H:i:s"));

        $stmt->execute();

        return $token;
    }

    protected function retrieve()
    {
        $stmt = $this->getConnection()->prepare(
            "
                SELECT `user_id`
                FROM `{$this->getTableName()}`
                WHERE
                    `token` = :token
                    AND (
                        `lifetime` IS NULL
                        OR (`modified` > DATE_SUB(`modified`, INTERVAL `lifetime` SECOND))
                    )
            "
        );

        $stmt->execute(
	        [
	            ":token" => $this->getToken()
            ]
        );

        $user_id = $stmt->fetchColumn();

        if ($user_id === false) {
            $user_id = null;
        }

        return $user_id;
    }

    protected function update(\DateTime $modified)
    {
        $stmt = $this->getConnection()->prepare(
            "
                UPDATE `{$this->getTableName()}`
                SET `modified` = :modified
                WHERE `token` = :token
            "
        );

        $stmt->execute(
	        [
	            ":token" => $this->getToken(),
                ":modified" => $modified->format("Y-m-d H:i:s")
            ]
        );

        return $this;
    }

    protected function delete()
    {
        $stmt = $this->getConnection()->prepare(
            "
                DELETE FROM `{$this->getTableName()}`
                WHERE `token` = :token
            "
        );

        $stmt->execute(
	        [
	            ":token" => $this->getToken()
            ]
        );

        return $this;
    }

    protected function gc()
    {
        $stmt = $this->getConnection()->prepare(
            "
                DELETE FROM `{$this->getTableName()}`
                WHERE (
                    `lifetime` IS NOT NULL
                    AND (DATE_ADD(`modified`, INTERVAL `lifetime` SECOND) <= :now)
                )
            "
        );

        $stmt->execute(
            [
	           ":now" => (new \DateTime())->format("Y-m-d H:i:s")
            ]
        );

        return $this;
    }

}
