<?php namespace Minextu\Ettc\Account;

use \Minextu\Ettc\AbstractEttcDatabaseTest;

class RankTest extends AbstractEttcDatabaseTest
{
    public function init()
    {
        // delete predefined ranks, since these interfere with testing
        $sql = "truncate ranks";
        $this->getDb()->getPdo()->prepare($sql)->execute();
    }

    private function createTestRank($title)
    {
        $rank = new Rank($this->getDb());

        $rank->setTitle($title);
        $rank->create();
    }

    public function testRankCanBeCreated()
    {
        $title = "Test Rank";
        $this->createTestRank($title);

        // check if rank would be in Database
        $this->assertEquals(1, $this->getConnection()->getRowCount('ranks'), "Inserting failed");

        // check if values are created correctly
        $queryTable = $this->getConnection()->createQueryTable('ranks', 'SELECT id,title FROM ranks');
        $expectedTable = $this->createFlatXmlDataSet(__DIR__."/RankTest.xml")->getTable("ranks");
        $this->assertTablesEqual($expectedTable, $queryTable);

        // load created rank again
        $rank = new Rank($this->getDb(), 1);
        $this->assertEquals($title, $rank->getTitle(), "Rank did not get created correctly");
    }

    public function testRankCanNotBeLoadedByInvalidId()
    {
        $title = "Test Rank";
        $this->createTestRank($title);

        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        // rank with id -1 does not exist
        $rank = new Rank($this->getDb(), -1);
    }

    public function testRankCanBeDeleted()
    {
        $title = "Test Rank";
        $this->createTestRank($title);

        // load created rank
        $rank = new Rank($this->getDb(), 1);
        $this->assertEquals($title, $rank->getTitle(), "Rank did not get created correctly");

        // delete this rank
        $rank->delete();

        // try to load this rank again
        $this->setExpectedException('Minextu\Ettc\Exception\InvalidId');
        $rank = new Rank($this->getDb(), 1);
    }

    public function testRankCanBeUpdated()
    {
        $oldTitle = "Test Rank";
        $newTitle = "Test Rank 2";
        $this->createTestRank($oldTitle);

        // load created rank
        $rank = new Rank($this->getDb(), 1);
        $this->assertEquals($oldTitle, $rank->getTitle(), "Rank did not get created correctly");

        // change title and update rank
        $rank->setTitle($newTitle);
        $rank->update();

        // load rank again and check title
        $rank = new Rank($this->getDb(), 1);
        $this->assertEquals($newTitle, $rank->getTitle(), "Rank title did not get updated");
    }

    public function testNonExistingRankCanNotBeUpdated()
    {
        $rank = new Rank($this->getDb());
        $newTitle = "Test Rank 2";
        $rank->setTitle($newTitle);

        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $rank->update();
    }

    public function testLoadedRankCanNotBeCreated()
    {
        $title = "Test Rank";
        $this->createTestRank($title);

        // load created rank
        $rank = new Rank($this->getDb(), 1);
        $this->assertEquals($title, $rank->getTitle(), "Rank did not get created correctly");

        // try to recreate the rank
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $createStatus = $rank->create();
    }

    public function testEmptyRankCanNotBeCreated()
    {
        $rank = new Rank($this->getDb());

        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $rank->create();
    }

    public function testRankWithoutTitleCanNotBeCreated()
    {
        $rank = new Rank($this->getDb());

        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $rank->create();
    }
}
