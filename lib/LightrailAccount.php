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
            $accountCard = LightrailCard::createAccountCardByContactId($params);
        } else {
            $accountCard = LightrailCard::createAccountCardByShopperId($params);
        }

        return $accountCard;
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
