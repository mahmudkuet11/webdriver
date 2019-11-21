<?php

namespace Mahmud\WebDriver;


use Mahmud\WebDriver\Tests\TestCase;

class BrowserTest extends TestCase {
    /**
     * @test
     */
    public function it_visits_a_url() {
        $browser = (new Browser())->get();
        $source = $browser->visit($this->fullUrl("/"))->remoteWebDriver->getPageSource();
        
        $this->assertContains("Hello mahmudkuet11/webdriver", $source);
        
        $browser->close();
    }
    
    /**
     * @test
     */
    public function it_can_refresh_page() {
        $browser = (new Browser())->get();
        
        $browser->visit($this->fullUrl("/"));
        
        $browser->remoteWebDriver->executeScript("document.write('')");
        
        $source = $browser->refresh()->remoteWebDriver->getPageSource();
        
        $this->assertContains("Hello mahmudkuet11/webdriver", $source);
        
        $browser->close();
    }
    
    /**
     * @test
     */
    public function it_can_go_back() {
        $browser = (new Browser())->get();
        
        $browser->visit($this->fullUrl("/"))
            ->visit("https://google.com")
            ->back();
        
        $this->assertEquals($browser->remoteWebDriver->getCurrentURL(), $this->fullUrl("/"));
        
        $browser->close();
    }
    
    /**
     * @test
     */
    public function it_can_clear_a_input_field() {
        $browser = (new Browser())->get();
    
        $browser->visit($this->fullUrl("/"))
            ->type("Hello", "#input1")
            ->clear("#input1");
        
        $this->assertEquals("", $browser->findElement("#input1")->getAttribute('value'));
    
        $browser->close();
    }
}
