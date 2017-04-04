<?php namespace Minextu\Ettc\Database\Migration;

class addPermissionsColumn extends AbstractMigration
{
    public function upgrade()
    {
        // add permissions column to users table
        $sql = '
        ALTER TABLE `users`
            ADD `permissions`
                TEXT NULL AFTER `rank`;
        ';
        $this->db->getPdo()->prepare($sql)->execute();

        // add permissions columnt to userApiKeys table
        $sql = '
        ALTER TABLE `userApiKeys`
            ADD `permissions`
                TEXT NULL AFTER `key`;
        ';
        $this->db->getPdo()->prepare($sql)->execute();

        // add permissions columnt to ranks table
        $sql = '
        ALTER TABLE `ranks`
            ADD `permissions`
                TEXT NULL AFTER `title`;
        ';
        $this->db->getPdo()->prepare($sql)->execute();
    }

    public function downgrade()
    {
        $sql = '
        ALTER TABLE `users` DROP `permissions`;
        ';
        $this->db->getPdo()->prepare($sql)->execute();

        $sql = '
        ALTER TABLE `userApiKeys` DROP `permissions`;
        ';
        $this->db->getPdo()->prepare($sql)->execute();

        $sql = '
        ALTER TABLE `ranks` DROP `permissions`;
        ';
        $this->db->getPdo()->prepare($sql)->execute();
    }
}
