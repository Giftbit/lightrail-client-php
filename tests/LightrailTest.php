<?php

namespace Lightrail;

require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailTest extends TestCase
{
	public function testEnvVarsSet()
	{
		$this->assertNotEmpty(getEnv("LIGHTRAIL_API_KEY"));
		$this->assertNotEmpty(getEnv("LIGHTRAIL_SHARED_SECRET"));
		$this->assertNotEmpty(getEnv("CONTACT_ID"));
		$this->assertNotEmpty(getEnv("SHOPPER_ID"));
	}

	public function testPing()
    {
        Lightrail::$apiKey = getEnv("LIGHTRAIL_API_KEY");
        $response = LightrailAPICall::ping();
        $this->assertEquals('TEST', $response['user']['mode']);
    }
}
