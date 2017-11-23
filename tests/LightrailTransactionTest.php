<?php

namespace Lightrail;

require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailTransactionTest extends TestCase {
	public static function setUpBeforeClass() {
		Lightrail::$apiKey = getEnv("LIGHTRAIL_API_KEY");
	}

	public function testSimulateByShopperId() {
		Lightrail::$apiKey = getEnv("LIGHTRAIL_API_KEY");
		$params            = $this->getBasicParams();
		$transaction       = LightrailTransaction::simulate( $params );
		$this->assertEquals( 'DRAWDOWN', $transaction->transactionType );
		$this->assertEquals( null, $transaction->transactionId );
	}

	public function getBasicParams() {
		return array(
			'value'     => - 1,
			'currency'  => "USD",
			'shopperId' => getEnv("SHOPPER_ID"),
		);
	}

	public function testSimulateByLowerCaseCurrency() {
		$params             = $this->getBasicParams();
		$params['currency'] = 'usd';
		$transaction        = LightrailTransaction::simulate( $params );
		$this->assertEquals( 'DRAWDOWN', $transaction->transactionType );
		$this->assertEquals( null, $transaction->transactionId );
	}

	public function testTransactionByShopperId() {
		$params = $this->getBasicParams();

		$transaction = LightrailTransaction::create( $params );
		$this->assertNotNull( $transaction->transactionId );
		$this->assertEquals( - 1, $transaction->value );
		$this->assertEquals( 'DRAWDOWN', $transaction->transactionType );

		$params['value'] = 1;
		$transaction     = LightrailTransaction::create( $params );
		$this->assertNotNull( $transaction->transactionId );
		$this->assertEquals( 1, $transaction->value );
		$this->assertEquals( 'FUND', $transaction->transactionType );
	}

	public function testTransactionByShopperIdWithUserSuppliedId() {
		$params                   = $this->getBasicParams();
		$params['userSuppliedId'] = uniqid();

		$transaction = LightrailTransaction::create( $params );
		$this->assertNotNull( $transaction->transactionId );
		$this->assertEquals( - 1, $transaction->value );
		$this->assertEquals( 'DRAWDOWN', $transaction->transactionType );
		$this->assertEquals( $params['userSuppliedId'], $transaction->userSuppliedId );

		$params['value'] = 1;
		unset( $params['userSuppliedId'] );
		$transaction = LightrailTransaction::create( $params );
		$this->assertNotNull( $transaction->transactionId );
		$this->assertEquals( 1, $transaction->value );
		$this->assertEquals( 'FUND', $transaction->transactionType );
	}

	public function testPendingCaptureRefund() {
		$params = $this->getBasicParams();

		$transaction = LightrailTransaction::createPending( $params );
		$this->assertNotNull( $transaction->transactionId );
		$this->assertEquals( - 1, $transaction->value );
		$this->assertEquals( 'PENDING_CREATE', $transaction->transactionType );

		$voidedTransaction = $transaction->void();
		$this->assertEquals( 'PENDING_VOID', $voidedTransaction->transactionType );

		$transaction        = LightrailTransaction::createPending( $params );
		$captureTransaction = $transaction->capture();
		$this->assertEquals( 'DRAWDOWN', $captureTransaction->transactionType );

		$refundTransaction = $captureTransaction->refund();
		$this->assertEquals( 'DRAWDOWN_REFUND', $refundTransaction->transactionType );
	}
}
