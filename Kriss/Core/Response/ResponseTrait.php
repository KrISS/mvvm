<?php

namespace Kriss\Core\Response;

trait ResponseTrait {
    private function sendHeadersBody($headers = [], $body = '') {
        foreach ($headers as $header) {
            header($header[0].(isset($header[1])?': ' . $header[1]:''));
        }
        echo $body;
    }
}
