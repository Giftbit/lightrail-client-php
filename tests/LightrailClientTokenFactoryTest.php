<?php

namespace Lightrail;

require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv( __DIR__ . "/.." );
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailClientTokenFactoryTest extends TestCase {
	public function testJWT() {
		Lightrail::$apiKey       = getEnv( "LIGHTRAIL_API_KEY" );
		Lightrail::$sharedSecret = getEnv( "LIGHTRAIL_SHARED_SECRET" );

		$token   = LightrailClientTokenFactory::generate( getEnv( "SHOPPER_ID" ), 10000 );
		$decoded = \Firebase\JWT\JWT::decode( $token, getEnv( "LIGHTRAIL_SHARED_SECRET" ), array( 'HS256' ) );
		$this->assertEquals( getEnv( "SHOPPER_ID" ), $decoded->g->shi );
	}

}
