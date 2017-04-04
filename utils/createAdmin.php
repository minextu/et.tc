<?php namespace Minextu\Ettc;

use Minextu\Ettc\Account\User;

require_once(__DIR__."/../src/autoload.php");

$ettc = new Ettc();

try {
    $userTest = new User($ettc->getDb(), 1);
    $aUserDoesExist = true;
} catch (Exception\Exception $e) {
    $aUserDoesExist = false;
}

if ($aUserDoesExist) {
    echo "A user was already created";
} else {
    $nick = readline("Nick: ");

    echo "Password: ";
    system('stty -echo');
    $pw = trim(fgets(STDIN));
    system('stty echo');
    echo "\n";

    if ($nick !== false && $pw !== false) {
        $user = new User($ettc->getDb());
        try {
            $user->setNick($nick);
            $user->setPassword($pw);
            // rank 2 = admin
            $user->setRank(2);
            $createStatus = $user->create();
        } catch (Exception\Exception $e) {
            echo $e->getMessage();
            $createStatus = false;
        }

        if ($createStatus) {
            echo "Success!";
        }
    }
}
