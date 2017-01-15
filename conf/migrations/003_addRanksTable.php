<?php namespace Minextu\Ettc\Database\Migration;

class addRanksTable extends AbstractMigration
{
    public function upgrade()
    {
        $sql = '
        CREATE TABLE `ranks`
        (
            `id` INT(255) UNSIGNED NULL AUTO_INCREMENT ,
            `title` VARCHAR(30) NOT NULL ,
            PRIMARY KEY (`id`)
        )';
        $this->db->getPdo()->prepare($sql)->execute();

        // add default ranks
        $sql = '
        INSERT into `ranks`
            (title) VALUES
            ("Guest")
        ';
        $this->db->getPdo()->prepare($sql)->execute();

        $sql = '
        INSERT into `ranks`
            (title) VALUES
            ("Admin")
        ';
        return $this->db->getPdo()->prepare($sql)->execute();
    }

    public function downgrade()
    {
        $sql = '
        DROP TABLE `ranks`
        ';

        return $this->db->getPdo()->prepare($sql)->execute();
    }
}
