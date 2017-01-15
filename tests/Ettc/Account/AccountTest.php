<?php namespace Minextu\Ettc\Account;

use \Minextu\Ettc\AbstractEttcDatabaseTest;

class AccountTest extends AbstractEttcDatabaseTest
{
    private function createTestUser()
    {
        $user = new User($this->getDb());

        // save user
        $nickname = "phpUnit_Test_User";
        $email = "testuser@nexttrex.de";
        $password = "abc123";

        $nicknameStatus = $user->setNick($nickname);
        $this->assertTrue($nicknameStatus, "setNick didn't return True");
        $emailStatus = $user->setEmail($email);
        $this->assertTrue($emailStatus, "setEmail didn't return True");
        $passwordStatus = $user->setPassword($password);
        $this->assertTrue($passwordStatus, "setPassword didn't return True");

        $createStatus = $user->create();
        $this->assertTrue($createStatus, "create didn't return True");
    }

    private function loginTestUser()
    {
        $userId = 1;

        // login the first user
        $user = new User($this->getDb(), $userId);
        $status = Account::login($user);
    }

    public function testUserCanBeLoggedIn()
    {
        $userId = 1;
        $this->createTestUser();
        $this->loginTestUser();

        // check if session is correct
        $this->assertEquals($userId, $_SESSION['ettc']['userId'], "session cookie 'userId' not correct");

        // check if login status can be checked
        $user = Account::checkLogin($this->getDb());
        $this->assertInstanceOf(User::class, $user);

        // check if id si correct
        $this->assertEquals($userId, $user->getId());
    }

    public function testUserCanBeLoggedOut()
    {
        $this->createTestUser();
        $this->loginTestUser();

        // check if user is logged in
        $this->assertInstanceOf(User::class, Account::checkLogin($this->getDb()));

        // logout user
        Account::logout();

        // check if session is correct
        $this->assertEmpty($_SESSION['ettc'], "session cookie 'userId' was not deleted");

        // check if user is logged out
        $this->assertEquals(false, Account::checkLogin($this->getDb()));
    }

    public function testUserIsNotLoggedInByDefault()
    {
        $this->createTestUser();

        // check if user is logged out
        $this->assertEquals(false, Account::checkLogin($this->getDb()));
    }

    public function testInvalidUserId()
    {
        $this->createTestUser();

        // User with id -1 does not exist
        $_SESSION['ettc']['userId'] = -1;

        // check if user is logged out
        $this->assertEquals(false, Account::checkLogin($this->getDb()), "User is logged in");

        // check if session was deleted again
        $this->assertEmpty($_SESSION['ettc'], "session cookie 'userId' was not deleted");
    }
}
