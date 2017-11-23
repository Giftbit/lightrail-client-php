<?php

namespace Lightrail;

class LightrailCard extends LightrailObject
{
    private static $JSON_ROOT_NAME = 'card';

    private static $CARDS_ENDPOINT = 'cards';

    public static function create($params)
    {
        Lightrail::checkCardParams($params);
        $endpoint = Lightrail::$API_BASE . self::$CARDS_ENDPOINT;
        $response = json_decode(LightrailAPICall::post($endpoint, $params), true);

        return new LightrailCard($response, self::$JSON_ROOT_NAME);
    }

    public static function createAccountCardByContactId($params)
    {
        Lightrail::checkAccountCardParamsByContactId($params);

        $contact = LightrailContact::retrieveByContactId($params['contactId']);

        try {
            return $contact->retrieveContactCardForCurrency($params['currency']);
        } catch (Exceptions\ObjectNotFoundException $e) {
            $cardCreationParams = self::addAccountCardParams($params);

            return self::create($cardCreationParams);
        }
    }

    public static function createAccountCardByShopperId($params)
    {
        Lightrail::checkAccountCardParamsByShopperId($params);

        $contact = LightrailContact::retrieveByShopperId($params['shopperId']);

        try {
            return $contact->retrieveContactCardForCurrency($params['currency']);
        } catch (Exceptions\ObjectNotFoundException $e) {
            $cardCreationParams = self::addAccountCardParams($params);

            return self::create($cardCreationParams);
        }
    }

    // Helpers

    private static function addAccountCardParams($params)
    {
        $newParams = $params;
        $newParams['cardType'] = 'ACCOUNT_CARD';
    }
}
