<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\WebDriver;


use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

class Browser {
    /**
     * @var bool
     */
    private $isHeadless = false;
    
    /**
     * @var int
     */
    private $width = 1920;
    
    /**
     * @var int
     */
    private $height = 1080;
    
    /**
     * @return Driver
     */
    public function get() {
        $options = (new ChromeOptions())->addArguments($this->getChromeArguments());
        
        $driver = RemoteWebDriver::create(
            'http://localhost:9515', DesiredCapabilities::chrome()->setCapability(ChromeOptions::CAPABILITY, $options)
        );
        
        return new Driver($driver);
    }
    
    protected function getChromeArguments() {
        $args = [
            "--disable-gpu",
            "--window-size={$this->width},{$this->height}"
        ];
        
        if ($this->isHeadless) {
            $args[] = "--headless";
        }
        
        return $args;
    }
    
    /**
     * @param bool $isHeadless
     *
     * @return $this
     */
    public function headless($isHeadless = true) {
        $this->isHeadless = $isHeadless;
        
        return $this;
    }
    
    /**
     * @param int $width
     * @param int $height
     *
     * @return $this
     */
    public function dimension($width, $height) {
        $this->width = $width;
        $this->height = $height;
        
        return $this;
    }
}
