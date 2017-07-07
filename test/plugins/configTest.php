<?php 

use Kriss\Core\Container\Container as Container;
use Kriss\Core\App\App as App;

include_once('plugins/config.php');

class configTest extends \PHPUnit\Framework\TestCase {

    private function getApp($withConfig = false) {
        $app = new App(new Container());

        if ($withConfig) {
            config($app);
        }
        
        return $app;
    }
    
    public function testWithoutConfigApp()
    {
        $app = $this->getApp();
        $this->assertSame(false, $app->getContainer()->has('Config'));
    }
    
    public function testWithConfigApp()
    {
        $app = $this->getApp(true);
        $this->assertSame(true, $app->getContainer()->has('Config'));
    }
}
