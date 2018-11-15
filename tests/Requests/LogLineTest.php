<?php

namespace Rebing\Timber\Tests\Requests;

use Rebing\Timber\Requests\LogLine;
use Rebing\Timber\Tests\TestCase;

class LogLineTest extends TestCase
{
    /**
     * @test
     */
    public function testCreatesNewJsonLogLine()
    {
        $message = str_random();
        $level   = LogLine::LOG_LEVEL_WARN;
        $context = [
            'user' => [
                'id' => str_random(),
            ],
        ];
        $event   = [
            'http_request' => [
                'method' => 'GET',
            ],
        ];

        $logLine  = new LogLine();
        $promise = $logLine->json($message, $context, $event, $level);

        $result = $promise->wait();
        $this->assertEquals('Accepted logs', $result->getBody()->getContents());
    }

}