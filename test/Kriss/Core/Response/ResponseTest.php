<?php

include_once 'ResponseTestTrait.php';

use Kriss\Core\Response\Response as Response;

class ResponseTest extends \PHPUnit\Framework\TestCase {
    use ResponseTestTrait;
    
    private function getResponse($body, $headers)
    {
        return new Response($body, $headers);
    }
}