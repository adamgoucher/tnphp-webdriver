<?php
namespace TrueNorthPhp;

require_once 'PHPWebDriver/WebDriverWait.php';
require_once 'PHPWebDriver/WebDriverBy.php';

class Schedule {
  private $locators = array(
    "session" => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'a[href*="REPLACE"]'),
    "closing keynote" => array(\PHPWebDriver_WebDriverBy::XPATH, '//div[text()="Being Grumpy For Fun And Profit"]'),
    "fancy iframe" => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'iframe[id^="fancybox-frame"]'),
    "title" => array(\PHPWebDriver_WebDriverBy::XPATH, '(//div[@class="span7"]//h3)[2]'),
    "text" => array(\PHPWebDriver_WebDriverBy::XPATH, '(//div[@class="span7"]//h3)[2]/following-sibling::p[1]'),
  );

  function __construct($session) {
    $this->session = $session;
  }

  function __get($property) {
    switch($property) {
      case "title":
      case "text":
        list($type, $string) = $this->locators[$property];
        $e = $this->session->element($type, $string);
        return $e->text();
      default:
        return $this->$property;
    }
  }

  function open() {
    $this->session->open("http://truenorthphp.ca/schedule.php");
    return $this;
  }
  
  function wait_until_loaded() {
    $w = new \PHPWebDriver_WebDriverWait($this->session, 30, 0.5, array("locator" => $this->locators['closing keynote']));
    $w->until(
      function($session, $extra_arguments) {
        list($type, $string) = $extra_arguments['locator'];
        return $session->element($type, $string);
      }
    );
    return $this;
  }
  
  function open_session($session_name) {
    $needles = array(' ', '!');
    $session_name = str_replace($needles, "_", $session_name);
    $session_name = strtolower($session_name);
    
    list($type, $string) = $this->locators["session"];
    $string = str_replace('REPLACE', $session_name, $string);
    $e = $this->session->element($type, $string);
    $e->click();

    $w = new \PHPWebDriver_WebDriverWait($this->session, 30, 0.5, array("locator" => $this->locators['fancy iframe']));
    $iframe = $w->until(
      function($session, $extra_arguments) {
        list($type, $string) = $extra_arguments['locator'];
        return $session->element($type, $string);
      }
    );
    $this->session->switch_to_frame($iframe);
  }
}