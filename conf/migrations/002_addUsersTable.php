<?php namespace Minextu\Ettc\Database\Migration;

class addUsersTable extends AbstractMigration
{
    public function upgrade()
    {
        $sql = '
        CREATE TABLE `users`
        (
            `id` INT(255) UNSIGNED NULL AUTO_INCREMENT ,
            `nick` VARCHAR(30) NOT NULL ,
            `email` VARCHAR(100) NULL ,
            `hash` VARCHAR(100) NULL DEFAULT NULL ,
            `rank` INT(255) NOT NULL,
            `registerDate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id`), UNIQUE (`nick`)
        )';

        return $this->db->getPdo()->prepare($sql)->execute();
    }

    public function downgrade()
    {
        $sql = '
        DROP TABLE `users`
        ';

        return $this->db->getPdo()->prepare($sql)->execute();
    }
}
