<?php 

include_once 'ResponseTestTrait.php';

use Kriss\Core\Response\ViewControllerResponse as Response;

class ViewControllerResponseTest extends \PHPUnit\Framework\TestCase {
    use ResponseTestTrait;
    
    private function getResponse($body, $headers)
    {
        $view = $this->getMockBuilder('Kriss\Mvvm\View\ViewInterface')->getMock();
        $view->expects($this->once())
            ->method('render')
            ->with()
            ->will($this->returnValue([$headers, $body]));

        $controller = $this->getMockBuilder('Kriss\Mvvm\Controller\ControllerInterface')->getMock();
        $controller->expects($this->once())
            ->method('action');

        return new Response($view, $controller);
    }
}
