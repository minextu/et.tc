<?php namespace Minextu\Ettc\Database\Migration;

class addPermissionsToDefaultRanks extends AbstractMigration
{
    public function upgrade()
    {
        // add permission 'all' to all admins
        $sql = "
        UPDATE `ranks` SET `permissions` = 'all,' WHERE `id` = 2
        ";
        $this->db->getPdo()->prepare($sql)->execute();
    }

    public function downgrade()
    {
        // remove permission 'all' from all admins
        $sql = "
        UPDATE `ranks` SET `permissions` = '' WHERE `id` = 2
        ";
        $this->db->getPdo()->prepare($sql)->execute();
    }
}
