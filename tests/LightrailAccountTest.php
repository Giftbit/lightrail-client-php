<?php

namespace Lightrail;

require_once __DIR__ . '/../init.php';

$dotenv = new \Dotenv\Dotenv(__DIR__ . "/..");
$dotenv->load();

use PHPUnit\Framework\TestCase;

class LightrailAccountTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        Lightrail::$apiKey = getEnv("LIGHTRAIL_API_KEY");
    }

    public function getBasicContactIdParams()
    {
        return array(
            'contactId'      => getEnv('CONTACT_ID'),
            'userSuppliedId' => uniqid(),
            'currency'       => 'USD',
        );
    }

    public function getBasicShopperIdParams()
    {
        return array(
            'shopperId'      => getEnv('SHOPPER_ID'),
            'userSuppliedId' => uniqid(),
            'currency'       => 'USD',
        );
    }

    public function testCreateByContactId()
    {
        $params      = $this->getBasicContactIdParams();
        $accountCard = LightrailAccount::create($params);
        $this->assertTrue(is_string($accountCard->cardId));
        $this->assertEquals('ACCOUNT_CARD', $accountCard->cardType);
    }

    public function testCreateByShopperId()
    {
        $params      = $this->getBasicShopperIdParams();
        $accountCard = LightrailAccount::create($params);
        $this->assertTrue(is_string($accountCard->cardId));
        $this->assertEquals('ACCOUNT_CARD', $accountCard->cardType);
    }

    public function testCreateTransactionByContactId()
    {
        $params             = $this->getBasicContactIdParams();
        $params['value']    = -1;
        $accountTransaction = LightrailAccount::createTransaction($params);
        $this->assertTrue(is_string($accountTransaction->transactionId));
    }

    public function testCreateTransactionByContactInsteadOfContactId()
    {
        $params            = $this->getBasicContactIdParams();
        $params['contact'] = $params['contactId'];
        unset($params['contactId']);
        $params['value']    = -1;
        $accountTransaction = LightrailAccount::createTransaction($params);
        $this->assertTrue(is_string($accountTransaction->transactionId));
    }

    public function testCreateTransactionByShopperId()
    {
        $params             = $this->getBasicShopperIdParams();
        $params['value']    = -1;
        $accountTransaction = LightrailAccount::createTransaction($params);
        $this->assertTrue(is_string($accountTransaction->transactionId));
    }

    public function testSimulateTransactionByContactId()
    {
        $params             = $this->getBasicContactIdParams();
        $params['contact']  = $params['contactId'];
        $params['value']    = -1;
        $accountTransaction = LightrailAccount::simulateTransaction($params);
        $this->assertTrue(is_string($accountTransaction->transactionType));
    }

    public function testSimulateTransactionByShopperId()
    {
        $params             = $this->getBasicShopperIdParams();
        $params['value']    = -1;
        $accountTransaction = LightrailAccount::simulateTransaction($params);
        $this->assertTrue(is_string($accountTransaction->transactionType));
    }

}