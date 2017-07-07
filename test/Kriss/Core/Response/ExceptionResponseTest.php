<?php

include_once 'ResponseTestTrait.php';

use Kriss\Core\Response\ExceptionResponse as Response;

class ExceptionResponseTest extends \PHPUnit\Framework\TestCase {
    use ResponseTestTrait;
    
    private function getResponse($body, $headers)
    {
        return new Response(new Exception($body), $headers);
    }
}