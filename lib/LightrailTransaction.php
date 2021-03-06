<?php

namespace Lightrail;

use Lightrail\Exceptions\BadParameterException;

class LightrailTransaction extends LightrailObject
{
    private static $CREATE_ENDPOINT = "cards/%s/transactions";

    private static $DRYRUN_ENDPOINT = "cards/%s/transactions/dryRun";

    private static $CAPTURE_ENDPOINT = "cards/%s/transactions/%s/capture";
    private static $VOID_ENDPOINT = "cards/%s/transactions/%s/void";
    private static $REFUND_ENDPOINT = "cards/%s/transactions/%s/refund";

    public static function simulate($params)
    {
        return self::createImpl($params, true);
    }

    public static function createPending($params)
    {
        $params['pending'] = true;
        return self::createImpl($params, false);
    }

    public static function create($params)
    {
        return self::createImpl($params, false);
    }

    private static function createImpl($params, $simulate = false)
    {
        Lightrail::checkApiKey();
        if ($simulate) {
            $endpoint = Lightrail::$API_BASE . self::$DRYRUN_ENDPOINT;
        } else {
            $endpoint = Lightrail::$API_BASE . self::$CREATE_ENDPOINT;
        }
        $params = self::translateParametersFromStripe($params);

        $params = self::getAccountCardFromContactIfContact($params);
        $params = self::addDefaultUserSuppliedIdIfNotProvided($params);
        if ($simulate) {
            $params = self::addDefaultNSFIfNotProvided($params);
        }


        if (!isset($params['cardId'])) {
            throw new Exceptions\BadParameterException('Must provide one of \'cardId\', \'contact\', or \'shopperId\'');
        }
        $cardId = $params['cardId'];
        unset($params['cardId']);

        $endpoint = sprintf($endpoint, $cardId);
        $response = json_decode(LightrailAPICall::post($endpoint, $params), true);

        return new LightrailTransaction($response, 'transaction');
    }

    private static function getAccountCardFromContactIfContact($params)
    {
        $new_params = $params;

        if (!isset($new_params['currency'])) {
            throw new Exceptions\BadParameterException('Must provide \'currency\'');
        }
        $currency = $new_params['currency'];

        if (isset($new_params['shopperId'])) {
            $shopperId = $new_params['shopperId'];
            unset($new_params['shopperId']);
            $card = LightrailContact::retrieveByShopperId($shopperId)->retrieveContactCardForCurrency($currency);
            $new_params['cardId'] = $card->cardId;
        } elseif (isset($new_params['contact']) || isset($new_params['contactId'])) {
            $contactId = self::translateContactIdIfPresent($new_params);
            unset($new_params['contact']);
            unset($new_params['contactId']);
            $card = LightrailContact::retrieveByContactId($contactId)->retrieveContactCardForCurrency($currency);
            $new_params['cardId'] = $card->cardId;
        }

        return $new_params;
    }

    public function capture($params = array())
    {
        return $this->finalizeTransaction('CAPTURE', $params);
    }

    public function void($params = array())
    {
        return $this->finalizeTransaction('VOID', $params);
    }

    public function refund($params = array())
    {
        return $this->finalizeTransaction('REFUND', $params);
    }

    private function finalizeTransaction($action, $params)
    {

        if ($this->transactionId == null) {
            throw new Exceptions\BadParameterException('Cannot call ' . $action . ' on a simulated transaction.');
        }
        if ('CAPTURE' == $action) {
            $endpoint = Lightrail::$API_BASE . self::$CAPTURE_ENDPOINT;
        } elseif ('VOID' == $action) {
            $endpoint = Lightrail::$API_BASE . self::$VOID_ENDPOINT;
        } elseif ('REFUND' == $action) {
            $endpoint = Lightrail::$API_BASE . self::$REFUND_ENDPOINT;
        } else {
            throw new Exceptions\BadParameterException('Undefined action: ' . $action);
        }
        $endpoint = sprintf($endpoint, $this->cardId, $this->transactionId);

        $params ['userSuppliedId'] = ($this->userSuppliedId) . '-' . $action;
        $response = json_decode(LightrailAPICall::post($endpoint, $params), true);

        return new LightrailTransaction($response, 'transaction');
    }

    public static function addDefaultUserSuppliedIdIfNotProvided($params)
    {
        $new_params = $params;
        if (!isset($new_params['userSuppliedId'])) {
            $new_params['userSuppliedId'] = uniqid();
        }

        return $new_params;
    }

    private static function addDefaultNSFIfNotProvided($params)
    {
        $new_params = $params;
        if (!isset($new_params['nsf'])) {
            $new_params['nsf'] = false;
        }

        return $new_params;
    }

    private static function translateParametersFromStripe($params)
    {
        $new_params = $params;
        if (isset($new_params['amount']) && !isset($new_params['value'])) {
            $new_params['value'] = 0 - $new_params['amount'];
            unset($new_params['amount']);
        }

        if (isset($new_params['idempotency-key']) && !isset($new_params['userSuppliedId'])) {
            $new_params['userSuppliedId'] = $new_params['idempotency-key'];
            unset($new_params['userSuppliedId']);
        }

        $new_params['currency'] = strtoupper($new_params['currency']);

        return $new_params;
    }

    private static function translateContactIdIfPresent($params)
    {
        $newParams = $params;
        if (isset($newParams['contactId'])
            && isset($newParams['contact'])
            && ($newParams['contactId'] != $newParams['contact'])
        ) {
            throw new BadParameterException('Must set only one of \'contactId\' or \'contact\' for transactions');
        } elseif (isset($newParams['contact'])) {
            $contactId = $newParams['contact'];
        } elseif (isset($newParams['contactId'])) {
            $contactId = $newParams['contactId'];
        } else {
            throw new BadParameterException('Must set one of \'contact\' or \'contactId\' for transactions');
        }

        return $contactId;
    }
}
