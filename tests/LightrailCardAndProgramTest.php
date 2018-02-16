<?php

namespace Lightrail;

require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailCardAndProgramTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        Lightrail::$apiKey = getEnv("LIGHTRAIL_API_KEY");
    }

    public function testCreateCard()
    {
        $programParams = array(
            'userSuppliedId' => uniqid(),
            'currency' => 'USD',
            'name' => 'php library unit test program',
            'valueStoreType' => 'PRINCIPAL',
            'codeValueMin' => 10,
            'codeValueMax' => 1000
        );
        $program = LightrailProgram::create($programParams);
        $this->assertTrue((is_string($program->programId)));

        $cardParams = array(
            'userSuppliedId' => uniqid(),
            'currency' => 'USD',
            'programId' => $program->programId,
            'initialValue' => 100
        );
        $card = LightrailCard::create($cardParams);
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
}
