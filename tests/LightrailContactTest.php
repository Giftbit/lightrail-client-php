<?php

namespace Lightrail;
require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv( __DIR__ . "/.." );
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailContactTest extends TestCase {
	public static function setUpBeforeClass() {
		Lightrail::$apiKey = getEnv( "LIGHTRAIL_API_KEY" );
	}

	public function testRetrieve() {
		$contactById        = LightrailContact::retrieveByContactId( getEnv( "CONTACT_ID" ) );
		$contactByShopperId = LightrailContact::retrieveByShopperId( getEnv( "SHOPPER_ID" ) );
		$this->assertEquals( getEnv( "CONTACT_ID" ), $contactByShopperId->contactId );
		$this->assertEquals( getEnv( "SHOPPER_ID" ), $contactById->userSuppliedId );

		$cardFromContactId = $contactById->retrieveContactCardForCurrency( 'USD' );
		$cardFromShopperId = $contactByShopperId->retrieveContactCardForCurrency( 'USD' );

		$this->assertEquals( $cardFromContactId->cardId, $cardFromShopperId->cardId );
	}

	public function testCreateWithUserSuppliedId() {
		$params  = array( 'userSuppliedId' => uniqid() );
		$contact = LightrailContact::create( $params );
		$this->assertEquals( $params['userSuppliedId'], $contact->userSuppliedId );
	}

	public function testCreateWithShopperId() {
		$params  = array( 'shopperId' => uniqid() );
		$contact = LightrailContact::create( $params );
		$this->assertEquals( $params['shopperId'], $contact->userSuppliedId );
	}

	public function testCreateWithUserSuppliedIdAndShopperId() {
		$params = array( 'shopperId' => uniqid(), 'userSuppliedId' => uniqid() );
		$this->expectException( BadParameterException::class );
		LightrailContact::create( $params );
	}
}
