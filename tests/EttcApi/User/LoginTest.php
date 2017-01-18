<?php namespace Minextu\EttcApi\User;

use Minextu\Ettc\AbstractEttcDatabaseTest;
use Minextu\Ettc\Account\User;
use Minextu\Ettc\Account\Account;

class LoginTest extends AbstractEttcDatabaseTest
{
    public function createTestUser($nickname, $password)
    {
        $user = new User($this->getDb());
        $user->setNick($nickname);
        $user->setPassword($password);
        $user->create();
    }

    public function testUserCanBeLoggedIn()
    {
        $nickname = "TestNickname";
        $password = "testPassword";
        $this->createTestUser($nickname, $password);

        $_POST['nickname'] = $nickname;
        $_POST['password'] = $password;

        $loginApi = new Login($this->getDb());
        $answer = $loginApi->post();

        $this->assertEquals(['success' => true], $answer, "Login wasn't successfull");

        // check login status unsing Account class
        $user = Account::checkLogin($this->getDb());
        $this->assertInstanceOf(User::class, $user);

        $this->assertEquals($nickname, $user->getNick());
    }

    public function testWrongPassword()
    {
        $nickname = "TestNickname";
        $password = "testPassword";
        $this->createTestUser($nickname, $password);

        $_POST['nickname'] = $nickname;
        $_POST['password'] = "wrong";

        $loginApi = new Login($this->getDb());
        $answer = $loginApi->post();

        $this->assertEquals(['error' => "WrongNicknameOrPassword"], $answer);
    }

    public function testWrongNickname()
    {
        $nickname = "TestNickname";
        $password = "testPassword";
        $this->createTestUser($nickname, $password);

        $_POST['nickname'] = "wrong";
        $_POST['password'] = $password;

        $loginApi = new Login($this->getDb());
        $answer = $loginApi->post();

        $this->assertEquals(['error' => "WrongNicknameOrPassword"], $answer);
    }

    public function testMissingNickname()
    {
        $_POST['password'] = "test";
        ;

        $loginApi = new Login($this->getDb());
        $answer = $loginApi->post();

        $this->assertEquals(['error' => "MissingValues"], $answer);
    }

    public function testMissingPassword()
    {
        $_POST['nickname'] = "test";

        $loginApi = new Login($this->getDb());
        $answer = $loginApi->post();

        $this->assertEquals(['error' => "MissingValues"], $answer);
    }

    public function testAlreadyLoggedIn()
    {
        // create test user
        $nickname = "TestNickname";
        $password = "testPassword";
        $this->createTestUser($nickname, $password);

        // login test user using account class
        $user = new User($this->getDb());
        $user->loadNick($nickname);
        Account::login($user);

        $_POST['nickname'] = $nickname;
        $_POST['password'] = $password;

        $loginApi = new Login($this->getDb());
        $answer = $loginApi->post();

        $this->assertEquals(['error' => 'AlreadyLoggedIn'], $answer, "Login was successfull, despite already being logged in");
    }
}
