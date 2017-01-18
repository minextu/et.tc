<?php namespace Minextu\EttcApi\User;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;

class LogoutTest extends AbstractEttcDatabaseTest
{
    public function createTestUser($nickname, $password)
    {
        $user = new User($this->getDb());
        $user->setNick($nickname);
        $user->setPassword($password);
        $user->create();
    }

    public function testUserCanBeLoggedOut()
    {
        $nickname = "TestNickname";
        $password = "testPassword";
        $this->createTestUser($nickname, $password);

        // login test user using account class
        $user = new User($this->getDb());
        $user->loadNick($nickname);
        Account::login($user);

        $logoutApi = new Logout($this->getDb());
        $answer = $logoutApi->post();

        $this->assertEquals(['success' => true], $answer, "Logout wasn't successfull");

        // check login status unsing Account class
        $user = Account::checkLogin($this->getDb());
        $this->assertFalse($user);
    }

    public function testAlreadyLoggedOutUserCanTBeLoggedOut()
    {
        $nickname = "TestNickname";
        $password = "testPassword";
        $this->createTestUser($nickname, $password);

        $logoutApi = new Logout($this->getDb());
        $answer = $logoutApi->post();

        $this->assertEquals(['error' => 'NotLoggedIn'], $answer, "Logout was successfull, despite already being logged out");

        // check login status unsing Account class
        $user = Account::checkLogin($this->getDb());
        $this->assertFalse($user);
    }
}
