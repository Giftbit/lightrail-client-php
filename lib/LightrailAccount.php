<?php

namespace Lightrail;

class LightrailAccount extends LightrailObject
{
    /**
     * @params $params an Array with: contactId or shopperId; currency; and userSuppliedId for card
     */
    public static function create($params)
    {
        Lightrail::checkAccountCardParams($params);
        if (isset($params['contactId'])) {
            $accountCard = self::createAccountCardByContactId($params);
        } elseif (isset($params['shopperId'])) {
            $accountCard = self::createAccountCardByShopperId($params);
        } else {
            throw new Exceptions\BadParameterException("must set one of: shopperId, contactId");
        }

        return $accountCard;
    }

    private static function createAccountCardByContactId($params)
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

    private static function createAccountCardByShopperId($params)
    {
        Lightrail::checkAccountCardParamsByShopperId($params);

        try {
            $contact = LightrailContact::retrieveByShopperId($params['shopperId']);
        } catch (Exceptions\ObjectNotFoundException $e) {
            $contact = LightrailContact::create(array('shopperId' => $params['shopperId']));
        }

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

    /**
     * @params $params an Array with: contactId or shopperId; currency; value; and userSuppliedId for transaction
     */
    public static function simulateTransaction($params)
    {
        Lightrail::checkAccountTransactionParams($params);

        return LightrailTransaction::simulate($params);
    }

    /**
     * @params $params an Array with: contactId or shopperId; currency; value; and userSuppliedId for transaction
     */
    public static function createTransaction($params)
    {
        Lightrail::checkAccountTransactionParams($params);

        return LightrailTransaction::create($params);
    }
}
