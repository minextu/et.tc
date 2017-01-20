<?php namespace Minextu\Ettc\Database\Migration;

class addProjectsTable extends AbstractMigration
{
    public function upgrade()
    {
        $sql = '
        CREATE TABLE `projects`
        (
            `id` INT(255) UNSIGNED NULL AUTO_INCREMENT ,
            `title` VARCHAR(100) NOT NULL ,
            `description` VARCHAR(10000) NOT NULL ,
            `image` VARCHAR(100) NULL ,
            `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            `updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
            PRIMARY KEY (`id`)
        )';

        return $this->db->getPdo()->prepare($sql)->execute();
    }

    public function downgrade()
    {
        $sql = '
        DROP TABLE `projects`
        ';

        return $this->db->getPdo()->prepare($sql)->execute();
    }
}
