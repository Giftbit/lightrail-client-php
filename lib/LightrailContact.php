<?php

namespace Lightrail;

class LightrailContact extends LightrailObject
{
    private static $JSON_ROOT_NAME = 'contact';
    private static $CONTACTS_ENDPOINT = 'contacts';
    private static $RETRIEVE_ENDPOINT = 'contacts/%s';
    private static $RETRIEVE_BY_SHOPPER_ID_ENDPOINT = 'contacts/?userSuppliedId=%s';
    private static $RETRIEVE_ACCOUNT_CARD_FOR_CURRENCY = 'cards?contactId=%s&cardType=ACCOUNT_CARD&currency=%s';

    public static function create($params)
    {
        Lightrail::checkContactParams($params);

        if (isset($params['userSuppliedId'])
            && isset($params['shopperId'])
            && ($params['userSuppliedId'] != $params['shopperId'])
        ) {
            throw new Exceptions\BadParameterException('Could not create contact: shopperId and userSuppliedId set to different values');
        } elseif (!isset($params['userSuppliedId']) && isset($params['shopperId'])) {
            $params['userSuppliedId'] = $params['shopperId'];
        }

        $endpoint = Lightrail::$API_BASE . self::$CONTACTS_ENDPOINT;
        $response = json_decode(LightrailAPICall::post($endpoint, $params), true);

        return new LightrailContact($response, self::$JSON_ROOT_NAME);
    }

    public static function retrieveByContactId($contactId)
    {
        $endpoint = sprintf(Lightrail::$API_BASE . self::$RETRIEVE_ENDPOINT, $contactId);
        $response = json_decode(LightrailAPICall::get($endpoint), true);

        return new LightrailContact($response, self::$JSON_ROOT_NAME);
    }

    public static function retrieveByShopperId($shopperId)
    {
        $endpoint = sprintf(Lightrail::$API_BASE . self::$RETRIEVE_BY_SHOPPER_ID_ENDPOINT, $shopperId);
        $response = json_decode(LightrailAPICall::get($endpoint), true);
        if (isset($response['contacts'][0])) {
            $response = $response['contacts'][0];
        } else {
            throw new Exceptions\ObjectNotFoundException('Could not find the Contact object for shopperId ' . $shopperId);
        }

        return new LightrailContact($response);
    }

    public function retrieveContactCardForCurrency($currency)
    {
        $endpoint = sprintf(
            Lightrail::$API_BASE . self::$RETRIEVE_ACCOUNT_CARD_FOR_CURRENCY,
            $this->contactId,
            $currency
        );
        $response = json_decode(LightrailAPICall::get($endpoint), true);
        if (isset($response['cards'][0])) {
            $response = $response['cards'][0];
        } else {
            throw new Exceptions\ObjectNotFoundException('Could not find a ' . $currency . ' Card for Contact ' . $this->contactId);
        }

        return new LightrailCard($response);
    }
}
