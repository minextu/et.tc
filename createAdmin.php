<?php namespace Minextu\Ettc;

use Minextu\Ettc\Account\User;

require_once("src/autoload.php");
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>et.tc create Admin</title>
</head>
<body>
    <h1>Create admin account</h1>

<?php
$rootDir = dirname($_SERVER['SCRIPT_NAME']);
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
    $nick = isset($_POST['nick']) ? $_POST['nick'] : false;
    $pw = isset($_POST['pw']) ? $_POST['pw'] : false;
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

    echo "
    <form action='#' method='POST'>
        Nick: <input type='text' name='nick'><br>
        Password: <input type='password' name='pw'><br>
        <input type='submit'>
    </form>";
}
?>
</body>
</html>
