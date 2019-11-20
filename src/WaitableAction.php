<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\WebDriver;


class WaitableAction {
    
    /**
     * @var Driver
     */
    private $driver;
    
    /**
     * WaitableAction constructor.
     *
     * @param Driver $driver
     */
    public function __construct(Driver $driver) {
        $this->driver = $driver;
    }
    
    public function __call($method, $arguments) {
        $this->driver->remoteWebDriver->wait()->until(function() use ($method, $arguments){
            try{
                $this->driver->{$method}(...$arguments);
                return true;
            }catch (\Exception $e){
                return false;
            }
        });
        
        return $this;
    }
}
