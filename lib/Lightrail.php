<?php

namespace Lightrail;

class Lightrail
{
    public static $apiKey;
    public static $sharedSecret;

    public static $API_BASE = 'https://api.lightrail.com/v1/';

    public static function setSharedSecret($theSharedSecret)
    {
        self::$sharedSecret = $theSharedSecret;
    }

    public static function setApiKey($theApiKey)
    {
        self::$apiKey = $theApiKey;
    }

    public static function checkApiKey()
    {
        if (!isset(self::$apiKey)) {
            throw new Exceptions\BadParameterException('Lightrail::$apiKey not set.');
        }
        if (empty(self::$apiKey)) {
            throw new Exceptions\BadParameterException('Lightrail::$apiKey is empty.');
        }
    }

    public static function checkCardParams($params)
    {
        self::checkApiKey();
        if (!(isset($params['cardType']) && $params['cardType'] == 'ACCOUNT_CARD') && !isset($params['userSuppliedId'])) {
            throw new Exceptions\BadParameterException('Card userSuppliedId not set.');
        }
        if (!isset($params['currency'])) {
            throw new Exceptions\BadParameterException('Card currency not set.');
        }
    }

    public static function checkAccountCardParams($params)
    {
        self::checkApiKey();

        if (!isset($params['userSuppliedId'])) {
            throw new Exceptions\BadParameterException('Account card userSuppliedId not set.');
        }
        if (!isset($params['currency'])) {
            throw new Exceptions\BadParameterException('Account currency not set.');
        }
        if (!isset($params['contactId']) && !isset($params['shopperId'])) {
            throw new Exceptions\BadParameterException('Must set one of \'contactId\' or \'shopperId\' for account card creation.');
        }
    }

    public static function checkAccountCardParamsByContactId($params)
    {
        self::checkApiKey();

        self::checkAccountCardParams($params);

        if (!isset($params['contactId'])) {
            throw new Exceptions\BadParameterException('Contact ID not set.');
        }
    }

    public static function checkAccountCardParamsByShopperId($params)
    {
        self::checkApiKey();

        self::checkAccountCardParams($params);

        if (!isset($params['shopperId'])) {
            throw new Exceptions\BadParameterException('Shopper ID not set.');
        }
    }

    public static function checkContactParams($params)
    {
        self::checkApiKey();
        if ((!isset($params['shopperId'])) && (!isset($params['userSuppliedId']))) {
            throw new Exceptions\BadParameterException('Must provide one of shopperId or userSuppliedId');
        }
    }

    public static function checkAccountTransactionParams($params)
    {
        self::checkApiKey();

        if (!isset($params['userSuppliedId'])) {
            throw new Exceptions\BadParameterException('Transaction userSuppliedId not set.');
        }
        if (!isset($params['currency'])) {
            throw new Exceptions\BadParameterException('Transaction currency not set.');
        }
        if (!isset($params['value']) && !isset($params['amount'])) {
            throw new Exceptions\BadParameterException('Transaction value not set.');
        }
        if (!isset($params['shopperId']) && !isset($params['contactId']) && !isset($params['contact'])) {
            throw new Exceptions\BadParameterException('Must set one of \'shopperId\', \'contactId\', or \'contact\' for account transaction.');
        }
    }

    public static function checkProgramParams($params)
    {
        self::checkApiKey();
        if (!isset($params['userSuppliedId'])) {
            throw new Exceptions\BadParameterException('Program userSuppliedId not set.');
        }
        if (!isset($params['currency'])) {
            throw new Exceptions\BadParameterException('Program currency not set.');
        }
        if (!isset($params['valueStoreType'])) {
            throw new Exceptions\BadParameterException('Program valueStoreType not set.');
        }
    }
}
