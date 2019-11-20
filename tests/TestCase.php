<?php
/**
 * Created by MD. Mahmud Ur Rahman <mahmud@mazegeek.com>.
 */

namespace Mahmud\WebDriver\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Symfony\Component\Process\Process;

class TestCase extends BaseTestCase {
    
    /**
     * @var Process
     */
    private $process;
    
    private $host = "127.0.0.1:5000";
    
    protected function setUp(): void {
        parent::setUp();
        
        $this->startServer();
    }
    
    private function startServer() {
        $this->process = new Process(["php", "-S", $this->host, "-t", __DIR__ . "/web/"]);
        $this->process->start();
    }
    
    protected function tearDown(): void {
        $this->process->stop(3, SIGINT);
        
        parent::tearDown();
    }
    
    public function fullUrl($path) {
        return "http://" . $this->host . $path;
    }
}
