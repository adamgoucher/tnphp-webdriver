<?php
namespace TrueNorthPhp;

require_once 'PHPWebDriver/WebDriver.php';
require_once dirname(__FILE__) . '/../../po/home.php';

class WebDriverSessionTest extends \PHPUnit_Framework_TestCase {
  protected static $session;

  public function setUp() {
    $driver = new \PHPWebDriver_WebDriver();
    self::$session = $driver->session();
    
  }

  public function tearDown() {
    self::$session->close();
  }
  
  /**
  * @test
  * @group session
  * @group first
  */
  public function testWebDriver() {
    $home_page = new Home(self::$session);
    $home_page->open();
    $schedule_page = $home_page->navigate_to('Schedule');
    $schedule_page->open_session('WebDriver!');
    $this->assertEquals($schedule_page->title, "WebDriver!");
  }
}
