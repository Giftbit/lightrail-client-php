<?php

namespace Lightrail;

class LightrailAccount extends LightrailObject
{
    /**
     * @params $params Array including either contactId or shopperId; currency; and userSuppliedId for card
     */
    public static function create($params)
    {
        if (isset($params['contactId'])) {
            $accountCard = LightrailCard::createAccountCardByContactId($params);
        } else {
            $accountCard = LightrailCard::createAccountCardByShopperId($params);
        }

        return $accountCard;
    }

    /**
     * @params $params Array including either contactId or shopperId; currency; value; and userSuppliedId for transaction
     */
    public static function simulateTransaction($params)
    {
        Lightrail::checkAccountTransactionParams($params);

        return LightrailTransaction::simulate($params);
    }

    /**
     * @params $params Array including either contactId or shopperId; currency; value; and userSuppliedId for transaction
     */
    public static function createTransaction($params)
    {
        Lightrail::checkAccountTransactionParams($params);

        return LightrailTransaction::create($params);
    }
}
