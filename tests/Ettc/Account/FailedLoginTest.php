<?php namespace Minextu\Ettc\Account;

use \Minextu\Ettc\AbstractEttcDatabaseTest;

class FailedLoginTest extends AbstractEttcDatabaseTest
{
    public function testFailedLoginCanBeCreated()
    {
        $nick = "TestNick";
        $_SERVER['REMOTE_ADDR'] = "127.0.0.1";

        $status = FailedLogin::add($this->getDb(), $nick);
        $this->assertTrue($status, "add failed");

        // check if value got saved
        $queryTable = $this->getConnection()->createQueryTable('failedLogins', 'SELECT id,nick,ip FROM failedLogins');
        $expectedTable = $this->createFlatXmlDataSet(__DIR__."/FailedLoginTest.xml")->getTable("failedLogins");
        $this->assertTablesEqual($expectedTable, $queryTable);

        // check if failed login can be loaded
        $lastLoginAttempt = FailedLogin::getLastTime($this->getDb(), $nick);
        $this->assertEquals(time(), strtotime($lastLoginAttempt), "Time of last login does not match", 5);
    }

    public function testLastLoginForUnloggedUser()
    {
        $nick = "TestNick2";

        // check if failed login won't get loaded
        $lastLoginAttempt = FailedLogin::getLastTime($this->getDb(), $nick);
        $this->assertFalse($lastLoginAttempt);
    }

    public function testOnMultibleEntriesTheNewestOneWillGetReturned()
    {
        $nick = "TestNick";
        $_SERVER['REMOTE_ADDR'] = "127.0.0.1";

        // add two failed logins with a delay of 2 seconds
        FailedLogin::add($this->getDb(), $nick);
        sleep(2);
        FailedLogin::add($this->getDb(), $nick);

        // check if newest entry will get returned
        $lastLoginAttempt = FailedLogin::getLastTime($this->getDb(), $nick);
        $this->assertEquals(time(), strtotime($lastLoginAttempt), "Time of last login does not match", 5);
    }
}
