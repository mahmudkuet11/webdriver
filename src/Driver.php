<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\WebDriver;


use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverPoint;
use Facebook\WebDriver\WebDriverSelect;
use Illuminate\Support\Str;

class Driver {
    /**
     * @var RemoteWebDriver
     */
    public $remoteWebDriver;
    
    /**
     * @var RemoteWebElement
     */
    private $parentElement;
    
    public function __construct(RemoteWebDriver $driver, RemoteWebElement $parentElement = null) {
        $this->remoteWebDriver = $driver;
        
        $this->parentElement = $parentElement;
    }
    
    public function visit($url) {
        $this->remoteWebDriver->navigate()->to($url);
        
        return $this;
    }
    
    public function refresh() {
        $this->remoteWebDriver->navigate()->refresh();
        
        return $this;
    }
    
    public function back() {
        $this->remoteWebDriver->navigate()->back();
        
        return $this;
    }
    
    public function maximize() {
        $this->remoteWebDriver->manage()->window()->maximize();
        
        return $this;
    }
    
    public function fitContent() {
        $body = $this->remoteWebDriver->findElement(WebDriverBy::tagName('body'));
        
        if (!empty($body)) {
            $this->resize($body->getSize()->getWidth(), $body->getSize()->getHeight());
        }
        
        return $this;
    }
    
    /**
     * Resize the browser window.
     *
     * @param  int $width
     * @param  int $height
     *
     * @return $this
     */
    public function resize($width, $height) {
        $this->remoteWebDriver->manage()->window()->setSize(
            new WebDriverDimension($width, $height)
        );
        
        return $this;
    }
    
    /**
     * Move the browser window.
     *
     * @param  int $x
     * @param  int $y
     *
     * @return $this
     */
    public function move($x, $y) {
        $this->remoteWebDriver->manage()->window()->setPosition(
            new WebDriverPoint($x, $y)
        );
        
        return $this;
    }
    
    /**
     * @param string $filePath
     *
     * @return $this
     */
    public function screenshot($filePath) {
        $this->remoteWebDriver->takeScreenshot($filePath);
        
        return $this;
    }
    
    public function ensurejQueryIsAvailable() {
        if ($this->remoteWebDriver->executeScript('return window.jQuery == null')) {
            $this->remoteWebDriver->executeScript(file_get_contents(__DIR__ . '/../bin/jquery.js'));
        }
    }
    
    public function pause($milliseconds) {
        usleep($milliseconds * 1000);
        
        return $this;
    }
    
    public function quit() {
        $this->remoteWebDriver->quit();
    }
    
    public function close() {
        $this->remoteWebDriver->close();
    }
    
    public function click($selector, $wait = false) {
        if ($wait) {
            return $this->wait()->click($selector);
        }
        
        $this->parentElement()->findElement(WebDriverBy::cssSelector($selector))->click();
        
        return $this;
    }
    
    /**
     * @return Driver|WaitableAction
     */
    public function wait() {
        return new WaitableAction($this);
    }
    
    protected function parentElement() {
        if (!$this->parentElement) {
            $this->parentElement = $this->remoteWebDriver->findElement(WebDriverBy::tagName('body'));
        }
        
        return $this->parentElement;
    }
    
    public function waitUntilVisible($selector) {
        $this->remoteWebDriver->wait()->until(
            WebDriverExpectedCondition::visibilityOfAnyElementLocated(WebDriverBy::cssSelector($selector))
        );
        
        return $this;
    }
    
    public function type($text, $selector, $wait = false) {
        if ($wait) {
            return $this->wait()->type($text, $selector);
        }
        
        $this->parentElement()->findElement(WebDriverBy::cssSelector($selector))->sendKeys($text);
        
        return $this;
    }
    
    public function uncheck($selector) {
        return $this->check($selector, false);
    }
    
    public function check($selector, $value = true) {
        $this->remoteWebDriver->executeScript(sprintf("document.querySelector('{$selector}').checked = %s;", $value ? "true" : "false"));
        
        return $this;
    }
    
    public function select($value, $selector) {
        (new WebDriverSelect($this->parentElement()->findElement(WebDriverBy::cssSelector($selector))))->selectByValue($value);
        
        return $this;
    }
    
    public function clear($selector) {
        $this->findElement($selector)->clear();
        
        return $this;
    }
    
    /**
     * @param string                $selector
     * @param RemoteWebElement|null $parentElement
     *
     * @return RemoteWebElement
     */
    public function findElement($selector, RemoteWebElement $parentElement = null) {
        $parentElement = $parentElement ?: $this->parentElement();
        return $parentElement->findElement(WebDriverBy::cssSelector($selector));
    }
    
    public function waitForElement($selector) {
        $this->remoteWebDriver->wait()->until(
            WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::cssSelector($selector))
        );
        
        return $this;
    }
    
    /**
     * @param string           $selector
     * @param RemoteWebElement $parentElement
     *
     * @return RemoteWebElement[]
     */
    public function findElements($selector, RemoteWebElement $parentElement = null) {
        $parentElement = $parentElement ?: $this->parentElement();
        return $parentElement->findElements(WebDriverBy::cssSelector($selector));
    }
    
    public function __call($name, $arguments) {
        if (Str::is("waitAnd*", $name)) {
            $method = lcfirst(Str::after($name, "waitAnd"));
            return $this->wait()->{$method}(...$arguments);
        }
        
        $this->remoteWebDriver->{$name}(...$arguments);
        
        return $this;
    }
}
