<?php namespace Minextu\Ettc\Account;

/**
 * Static class used log and get failed logins
 */
class FailedLogin
{
    /**
     * Add a failed login attempt, will also log ip address
     * @param   \Minextu\Ettc\Database\DatabaseInterface   $db    Database to be sued
     * @param   string   $nick   Nickname to log
     */
    public static function add($db, $nick)
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        $sql = 'INSERT into failedLogins
                (nick, ip)
                VALUES (?, ?)';
        $stmt = $db->getPdo()->prepare($sql);
        $status = $stmt->execute([$nick, $ip]);

        return $status;
    }

    /**
     * Try to fetch the time of last login attempt
     * @param   \Minextu\Ettc\Database\DatabaseInterface   $db    Database to be sued
     * @param   string   $nick   Nickname that was logged
     * @return                   Mysql time of last login attempt, or False if non exist
     */
    public static function getLastTime($db, $nick)
    {
        $sql = 'SELECT `time` FROM failedLogins WHERE nick=? ORDER BY `time` DESC LIMIT 1';

        $stmt = $db->getPdo()->prepare($sql);
        $stmt->execute([$nick]);

        $time = $stmt->fetchColumn();
        return $time;
    }
}
