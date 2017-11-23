<?php

namespace Lightrail;

require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailCardTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        Lightrail::$apiKey = getEnv("LIGHTRAIL_API_KEY");
    }

    public function getBasicParams()
    {
        return array(
            'userSuppliedId' => uniqid(),
            'currency' => 'USD',
        );
    }

    public function testCreateCard()
    {
        $params = $this->getBasicParams();
        $card = LightrailCard::create($params);
        $this->assertTrue((is_string($card->cardId)));
    }

//	Need a different way to test this: initial value is not returned in the response
//  public function testCreateCardWithInitialValue() {
//		$params                 = $this->getBasicParams();
//		$params['initialValue'] = 123;
//		$card                   = LightrailCard::create( $params );
//		var_dump( $card );
//		$this->assertEquals( 123, $card->initialValue );
//	}

    public function testCreateAccountCardByContactId()
    {
        $params = $this->getBasicParams();
        $params['contactId'] = getEnv("CONTACT_ID");
        $card = LightrailCard::createAccountCardByContactId($params);
        $this->assertEquals('ACCOUNT_CARD', $card->cardType);
    }

    public function testCreateAccountCardByShopperId()
    {
        $params = $this->getBasicParams();
        $params['shopperId'] = getEnv("SHOPPER_ID");
        $card = LightrailCard::createAccountCardByShopperId($params);
        $this->assertEquals('ACCOUNT_CARD', $card->cardType);
    }
}
