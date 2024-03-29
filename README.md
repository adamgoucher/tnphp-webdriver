WebDriver!
==========

Boilerplate
-----------

This is the supporting material for my session at [True North PHP](http://truenorthphp.ca). It was certainly not a tutorial, but instead was a quick introduction to things, and plant some of my favourite rants into people's (unsuspecting) heads.

Important Introductory Bits
---------------------------

* WebDriver is _just_ a library. It is _not a framework_
* It is also a [draft] W3C standard
* It will not _test_ your application, it will _drive_ your browser
* Geeks are _bad_ at naming. [Names Matter](http://www.slideshare.net/agoucher/namesmatter) somewhat explains the various moving parts -- and how to refer to things.
* All (current) PHP bindings use Remote WebDriver
* WebDriver for PHP is a very splinter-y place; I use my [fork of Facebook's implementation](https://github.com/Element-34/php-webdriver) -- but spend the time to experiment with them all and see which one aligns with your own set of opinions. There is lots of howto-ish things in that repo...
* There are two halves, well, its more like 20/80 parts; the 'session' and 'elements'

Setup
-----

All the moving parts for PHPWebDriver and this project uses are installable through PEAR.

```console
$ sudo pear channel-discover pear.phpunit.de
$ sudo pear install phpunit/PHPUnit
$ sudo pear channel-discover element-34.github.com/pear
$ sudo pear install -f element-34/SaunterPHP
$ sudo pear install -f element-34/PHPBrowserMobProxy
```

Page Objects
------------

When you are writing scripts you will end up with a _script_ and one or more _page object_.

Scripts...

* are collected by the _runner_
* executes methods (actions) on page objects
* contains asserts
* are pretty un-interesting

Page Objects...

* represent either a full page or part of a page
* find and interact with elements
* does not contain elements that are in other elements
* synchronize

[A Sample Page Object](https://gist.github.com/3429609)

Synchronization
---------------

Synchronization is hard (let's go shopping!). The important thing to remember is _Explicit is better than Implicit_.

```php
$w = new \PHPWebDriver_WebDriverWait($this->session, 30, 0.5, array("locator" => $this->locators['fancy iframe']));
$iframe = $w->until(
  function($session, $extra_arguments) {
    list($type, $string) = $extra_arguments['locator'];
    return $session->element($type, $string);
  }
);
```
    
Third Party Crap
----------------

If you are not responsible for developing something on your application, it shouldn't be there in your automation environment. Use feature switches, but if you don't have that, blacklist through a proxy.

```php
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
```
    
Javascript Executor
-------------------

Welcome to the future... where Canvas and ^$(*@)-ing JS widgets rule the world. These are black boxes [of pain].

```php
$this->session->execute(array(
                            "script" => 'return document.title;',
                            "args" => array()
                            )
                          );
```
    
The args key can also take a reference to an Element that can be used in the script itself. Ooooo. Meta.

```php
$options = array("chain" => $chain, "attrName" => $property);
$this->session->execute(array(
                             "script" => 'return arguments[0].fp_getPropertyValue(arguments[1]);',
                             "args" => array(array("ELEMENT" => $this->movie->getID()),
                                             $options)
                             ));
```
    
Just remember to _return_ a value if you are not just poking things.

Mobile?
-------

WebDriver has _desired_ capabilities and _required_ capabilities. (Or will have required capabilities soon-ish.)

```php
$this->session = self::$driver->session("android");
$this->session = self::$driver->session("iphone");
$this->session = self::$driver->session("ipad");
```
    
To the Cloud!
-------------

You do not want to deal with the browser permutation madness... outsource it! To something like Sauce Labs. Since WebDriver [on PHP] is Remote WebDriver, just point it at their server.

```php
$username = "yourusername";
$key = "your key";
$command_executor = "http://" . $username . ":" . $key . "@ondemand.saucelabs.com:80/wd/hub";
self::$driver = new PHPWebDriver_WebDriver($command_executor);
```
    
disclaimer: I'm a Sauce Labs partner, ask me for my partner discount code

Frameworks
----------

WebDriver is actually _really_ low level and you will want to write some helper-ish things around it. [SaunterPHP](http://element34.ca/products/saunter/saunterphp) is one such framework based on the last decade of me doing this sort of thing. (And Open Source. Of course!)