<?php

namespace Mahmud\WebDriver;


use Mahmud\WebDriver\Tests\TestCase;

class BrowserTest extends TestCase {
    protected function getPackageProviders($app) {
        return [
            WebDriverServiceProvider::class
        ];
    }
    
    /**
     * @test
     */
    public function it_visits_a_url() {
        $browser = (new Browser())->get();
        $source = $browser->visit($this->fullUrl("/"))->remoteWebDriver->getPageSource();
        
        $this->assertContains("Hello mahmudkuet11/webdriver", $source);
        
        $browser->close();
    }
}
