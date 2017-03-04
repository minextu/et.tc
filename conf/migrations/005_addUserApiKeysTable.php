<?php namespace Minextu\Ettc\Database\Migration;

class addUserApiKeysTable extends AbstractMigration
{
    public function upgrade()
    {
        $sql = '
        CREATE TABLE `userApiKeys`
        (
            `id` INT(255) UNSIGNED NULL AUTO_INCREMENT ,
            `title` VARCHAR(1000) NULL ,
            `userId` INT(255) UNSIGNED NULL ,
            `key` VARCHAR(100) NOT NULL ,
            `used` TIMESTAMP NULL DEFAULT NULL ,
            `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id`), UNIQUE (`key`)
        )';

        return $this->db->getPdo()->prepare($sql)->execute();
    }

    public function downgrade()
    {
        $sql = '
        DROP TABLE `userApiKeys`
        ';

        return $this->db->getPdo()->prepare($sql)->execute();
    }
}
