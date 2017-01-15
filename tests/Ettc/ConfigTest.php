<?php namespace Minextu\Ettc;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        // delete possible old test config
        $oldTestConfig = __DIR__.'/../../conf/config.phpUnitTest.php';
        if (file_exists($oldTestConfig)) {
            unlink($oldTestConfig);
        }
    }

    public static function tearDowAfterClass()
    {
        self::setUpBeforeClass();
    }

    public function testConfigFileCanBeCreatedAndLoaded()
    {
        $configTestOption = true;
        $configTestOption2 = 'Test String';

        $config = new Config('conf/config.phpUnitTest.php');
        $config->create();
        $config->set('testOption', $configTestOption);
        $success = $config->set('testOption2', $configTestOption2);

        $this->assertTrue($success);
        $this->assertFileExists('conf/config.phpUnitTest.php');

        // Check if the config file can be loaded again
        $config = new Config('conf/config.phpUnitTest.php');
        $success = $config->load();

        $this->assertTrue($success);

        $testOption = $config->get('testOption');
        $testOption2 = $config->get('testOption2');

        $this->assertEquals($testOption, $configTestOption);
        $this->assertEquals($testOption2, $configTestOption2);
    }

    public function testOptionCanBeAddedToExistingConfig()
    {
        $configTestOption3 = false;

        $config = new Config('conf/config.phpUnitTest.php');
        $success = $config->load();
        $this->assertTrue($success);

        $config->set('testOption3', $configTestOption3);

        $config = new Config('conf/config.phpUnitTest.php');
        $config->load();
        $testOption3 = $config->get('testOption3');
        $this->assertEquals($testOption3, $configTestOption3);
    }

    public function testLoadNonExistingConfig()
    {
        $config = new Config('conf/doesNotExist.php');
        $success = $config->load();

        $this->assertFalse($success);
    }

    public function testOptionsCanNotBeReadWhenNotLoaded()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $config = new Config('conf/config.phpUnitTest.php');
        $config->get('testOption');
    }

    public function testOptionCanNotBeAddedWhenNotLoaded()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $config = new Config('conf/config.phpUnitTest.php');
        $config->set('testOption', true);
    }

    public function testConfigCanNotBeOverwritten()
    {
        $this->setExpectedException('Minextu\Ettc\Exception\Exception');
        $config = new Config('conf/config.phpUnitTest.php');
        $config->create();
    }
}
