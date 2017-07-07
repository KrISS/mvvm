<?php

use Kriss\Core\App\App as App;

class AppTest extends \PHPUnit\Framework\TestCase {
    function testDefaultApp()
    {
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->at(0))
            ->method('getMethod')
            ->will($this->returnValue(''));;
        $request->expects($this->at(1))
            ->method('getPathInfo')
            ->will($this->returnValue(''));;

        $response = $this->getMockBuilder('Kriss\Mvvm\Response\ResponseInterface')->getMock();
        $response->expects($this->once())
            ->method('send');

        $router = $this->getMockBuilder('Kriss\Mvvm\Router\RouterInterface')->getMock();
        $router->expects($this->once())
            ->method('dispatch')
            ->will($this->returnValue($response));

        $container = $this->getMockBuilder('Kriss\Mvvm\Container\ContainerInterface')->getMock();
        $container->expects($this->at(0))
            ->method('set');
        $container->expects($this->at(1))
            ->method('set');
        $container->expects($this->at(2))
            ->method('get')
            ->with('Request')
            ->will($this->returnValue($request));
        $container->expects($this->at(3))
            ->method('get')
            ->with('Router')
            ->will($this->returnValue($router));

        $app = new App($container);
        $this->assertSame($container, $app->getContainer());
        $app->run();
    }
}