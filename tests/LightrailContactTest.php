<?php

namespace Lightrail;
require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailContactTest extends TestCase
{

    public function testRetrieve()
    {
        Lightrail::$apiKey = getEnv("LIGHTRAIL_API_KEY");
        $contactById = LightrailContact::retrieveByContactId(getEnv("CONTACT_ID"));
        $contactByShopperId = LightrailContact::retrieveByShopperId(getEnv("SHOPPER_ID"));
        $this->assertEquals(getEnv("CONTACT_ID"), $contactByShopperId->contactId);
        $this->assertEquals(getEnv("SHOPPER_ID"), $contactById->userSuppliedId);

        $cardFromContactId = $contactById->retrieveContactCardForCurrency('USD');
        $cardFromShopperId = $contactByShopperId->retrieveContactCardForCurrency('USD');

        $this->assertEquals($cardFromContactId->cardId, $cardFromShopperId->cardId);
    }

}
