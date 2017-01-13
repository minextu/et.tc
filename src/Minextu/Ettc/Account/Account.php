<?php namespace Minextu\Ettc\Account;
use \Minextu\Ettc\Exception\Exception;
/**
 * Static class used to login and logout using the session cookie
 */
class Account
{
    /**
     * Check if the current user is logged in
     * @param    \Minextu\Ettc\Database\DatabaseInterface   $db   Main database
     * @return   bool|User                                         Matching user object when logged in, false otherwise
     */
    static function checkLogin($db)
    {
        if (!isset($_SESSION['ettc']['userId']))
            $user = false;
        else
        {
            try
            {
                $user = new User($db, $_SESSION['ettc']['userId']);
            }
            catch(Exception $e)
            {
                self::logout();
                $user = false;
            }
        }

        return $user;
    }

    /**
     * Sets the users session to logged in
     * @param    User   $user     The user that was logged in
     */
    static function login($user)
    {
        $_SESSION['ettc']['userId'] = $user->getId();
    }

    /**
     * Sets the users session to be logged out
     */
    static function logout()
    {
        unset($_SESSION['ettc']['userId']);
    }
}
