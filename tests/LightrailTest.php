<?php

namespace Lightrail;

require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailTest extends TestCase
{
    public function testPing()
    {
        Lightrail::$apiKey = getEnv("LIGHTRAIL_API_KEY");
        $response = LightrailAPICall::ping();
        $this->assertEquals('TEST', $response['user']['mode']);
    }
}
