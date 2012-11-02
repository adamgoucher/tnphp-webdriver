<?php
namespace TrueNorthPhp;

require_once 'PHPWebDriver/WebDriverWait.php';
require_once 'PHPWebDriver/WebDriverBy.php';

class Home {
  private $locators = array(
    "fork me ribbon" => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, '.forkme-ribbon'),
    "schedule" => array(\PHPWebDriver_WebDriverBy::CSS_SELECTOR, 'div.navbar-fixed-top a[href="schedule.php"]'),    
  );

  function __construct($session) {
    $this->session = $session;
  }

  function __get($property) {
    switch($property) {
      case "js_title":
        return $this->session->execute(array(
                                        "script" => 'return document.title;',
                                        "args" => array()
                                        )
                                      );
      default:
        return $this->$property;
    }
  }

  function open() {
    $this->session->open("http://truenorthphp.ca/index.php");
    return $this;
  }
  
  function wait_until_loaded() {
    $w = new \PHPWebDriver_WebDriverWait($this->session, 30, 0.5, array("locator" => $this->locators['fork me ribbon']));
    $w->until(
      function($session, $extra_arguments) {
        list($type, $string) = $extra_arguments['locator'];
        return $session->element($type, $string);
      }
    );
    return $this;
  }
    
  function navigate_to($tab) {
    list($type, $string) = $this->locators[strtolower($tab)];
    $e = $this->session->element($type, $string);
    $e->click();

    require_once strtolower($tab) . ".php";
    $to_eval = "return new TrueNorthPhp\\" . ucfirst($tab) . '($this->session);'; 
    $p = eval($to_eval);
    $p->wait_until_loaded();
    return $p;
  }
}