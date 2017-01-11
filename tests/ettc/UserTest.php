<?php namespace nexttrex\Ettc;

class UserTest extends AbstractEttcDatabaseTest
{
    private function createTestUser()
    {
        $user = new User($this->getDb());

        // save user
        $nickname = "phpUnit_Test_User";
        $email = "testuser@nexttrex.de";
        $password = "abc123";

        $nicknameStatus = $user->setNick($nickname);
        $this->assertTrue($nicknameStatus);
        $emailStatus = $user->setEmail($email);
        $this->assertTrue($emailStatus);
        $passwordStatus = $user->setPassword($password);
        $this->assertTrue($passwordStatus);

        $createStatus = $user->create();
        $this->assertTrue($createStatus);
    }

    public function testUserCanBeCreated()
    {
        // create test user
        $this->createTestUser();

        // check if user would be in Database
        $this->assertEquals(1, $this->getConnection()->getRowCount('users'), "Inserting failed");

        // check if values are saved correctly
        $queryTable = $this->getConnection()->createQueryTable('users', 'SELECT id,nick,email FROM users');
        $expectedTable = $this->createFlatXmlDataSet(__DIR__."/UserTest.xml")->getTable("users");
        $this->assertTablesEqual($expectedTable, $queryTable);
    }

    public function testUserCanBeLoaded()
    {
        // create test user
        $this->createTestUser();

        $user = new User($this->getDb());

        $loadStatus = $user->loadNick("phpUnit_Test_User");
        $this->assertTrue($loadStatus);

        $nick = $user->getNick();
        $this->assertEquals("phpUnit_Test_User", $nick);
    }

    public function testUserCanBeLoadedById()
    {
        $this->createTestUser();

        // find user by name
        $user = new User($this->getDb());
        $loadStatus = $user->loadNick("phpUnit_Test_User");
        $this->assertTrue($loadStatus);
        $userId = $user->getId();

        // Load that user by id
        $user = new User($this->getDb(), $userId);
        $this->assertEquals($userId, $user->getId());

        $nick = $user->getNick();
        $this->assertEquals("phpUnit_Test_User", $nick);
    }

    public function testUserCanNotBeLoadedByInvalidId()
    {
        $this->createTestUser();

        $this->setExpectedException('nexttrex\Ettc\Exception\Exception');
        // user with id -1 does not exist
        $user = new User($this->getDb(), -1);
    }

    public function testLoadedUserCanNotBeCreated()
    {
        $this->createTestUser();

        $this->setExpectedException('nexttrex\Ettc\Exception\Exception');

        $user = new User($this->getDb());

        $loadStatus = $user->loadNick("phpUnit_Test_User");
        $this->assertTrue($loadStatus);

        $createStatus = $user->create();
    }

    public function testEmptyUserCanNotBeCreated()
    {
        $this->setExpectedException('nexttrex\Ettc\Exception\Exception');

        $user = new User($this->getDb());

        $createStatus = $user->create();
    }

    /*
     * Nickname Checks
    */

    public function testValidNickname()
    {
        $user = new User($this->getDb());
        $nickStatus = $user->setNick("phpunit_test_user_nondb");
        $this->assertTrue($nickStatus);
    }

    public function testAlreadyExistingNickname()
    {
        $this->createTestUser();

        $this->setExpectedException('nexttrex\Ettc\Exception\InvalidNickname');
        $user = new User($this->getDb());
        $nickStatus = $user->setNick("phpunit_test_user");
    }

    public function testEmptyNickname()
    {
        $this->setExpectedException('nexttrex\Ettc\Exception\InvalidNickname');
        $user = new User($this->getDb());
        $nickStatus = $user->setNick("");
    }

    public function testInvalidShortNickname()
    {
        // A Username shorter than 3 Characters should be invalid
        $this->setExpectedException('nexttrex\Ettc\Exception\InvalidNickname');
        $user = new User($this->getDb());
        $nickStatus = $user->setNick("12");
    }

    public function testInvalidLongNickname()
    {
        // A Username longer than 30 characters should be invalid
        $this->setExpectedException('nexttrex\Ettc\Exception\InvalidNickname');

        // generate A Nickname with 31 Characters
        $nickname = "";
        for ($i = 1; $i <= 31; $i++) {
            $nickname .= "a";
        }

        $user = new User($this->getDb());
        $nickStatus = $user->setNick($nickname);
    }

    /*
     * Password Checks
     */

     public function testValidPasswordCheck()
     {
         $this->createTestUser();

         $user = new User($this->getDb());
         $loadStatus = $user->loadNick("phpUnit_Test_User");
         $this->assertTrue($loadStatus);

         $password = "abc123";

         $validPassword = $user->checkPassword($password);
         $this->assertTrue($validPassword);
     }

    public function testInvalidPasswordCheck()
    {
        $this->createTestUser();

        $user = new User($this->getDb());
        $loadStatus = $user->loadNick("phpUnit_Test_User");
        $this->assertTrue($loadStatus);

        $password = "wrong password";

        $validPassword = $user->checkPassword($password);
        $this->assertFalse($validPassword);
    }

	public function testEmptyPasswordCheck()
	{
		$this->createTestUser();

		$user = new User($this->getDb());
		$loadStatus = $user->loadNick("phpUnit_Test_User");
		$this->assertTrue($loadStatus);

		$password = "";

		$validPassword = $user->checkPassword($password);
		$this->assertFalse($validPassword);
	}

    public function testInvalidShortPassword()
    {
        // A Password shorter than 6 characters should be invalid
        $this->setExpectedException('nexttrex\Ettc\Exception\InvalidPassword');

        $user = new User($this->getDb());
        $passwordStatus = $user->setPassword("abc12");
    }
}
