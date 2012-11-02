<?php
namespace TrueNorthPhp;

require_once 'PHPWebDriver/WebDriver.php';
require_once 'PHPWebDriver/WebDriverProxy.php';
require_once 'PHPBrowserMobProxy/Client.php';

require_once dirname(__FILE__) . '/../../po/home.php';

class WebDriverSessionWithExecutorTest extends \PHPUnit_Framework_TestCase {
  protected static $session;
  protected static $client;

  public function setUp() {
    $driver = new \PHPWebDriver_WebDriver();
    self::$client = new \PHPBrowserMobProxy_Client("localhost:8080");
    
    $additional_capabilities = array();
    $proxy = new \PHPWebDriver_WebDriverProxy();
    $proxy->httpProxy = self::$client->url;
    $proxy->add_to_capabilities($additional_capabilities);
    self::$session = $driver->session('firefox', $additional_capabilities);
    
    self::$client->blacklist('.*\/favicon\.ico/.*', 306);
    self::$client->blacklist('.*\.facebook\.net/.*', 306);
    self::$client->blacklist('.*\.twitter\.com/.*', 306);
    self::$client->blacklist('.*\.github\.com/.*', 306);
  }

  public function tearDown() {
    self::$session->close();
    self::$client->close();
  }
  
  /**
  * @test
  * @group session
  * @group executor
  */
  public function testWebDriver() {
    $home_page = new Home(self::$session);
    $home_page->open();
    $this->assertEquals($home_page->js_title, "True North PHP Conference - November 2-3 - Toronto, Canada");
  }
}
