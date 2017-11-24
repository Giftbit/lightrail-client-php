<?php

namespace Lightrail;

use Lightrail\Exceptions\BadParameterException;

class LightrailAccount extends LightrailObject
{
    private static $JSON_ROOT_NAME_ACCOUNT_CARD = 'card';
    private static $JSON_ROOT_NAME_TRANSACTION = 'transaction';

    private static $RETRIEVE_ENDPOINT = 'cards?userSuppliedId=%s&currency=%s';
    private static $RETRIEVE_BY_SHOPPER_ID_ENDPOINT = '';
    private static $RETRIEVE_ACCOUNT_CARD_FOR_CURRENCY = '';


    // Requires either contactId or shopperId, currency, and card userSuppliedId
    public static function create($params)
    {
        if (isset($params['contactId'])) {
            $accountCard = LightrailCard::createAccountCardByContactId($params);
        } else {
//            $paramsWithShopperId = self::userSuppliedIdToShopperId($params);
            $accountCard = LightrailCard::createAccountCardByShopperId($params);
        }

        return $accountCard;
    }

//    public static function createByContactId($params)
//    {
//        return LightrailCard::createAccountCardByContactId($params);
//    }
//
//    public static function createByShopperId($params)
//    {
//        return LightrailCard::createAccountCardByShopperId($params);
//    }


    public static function simulateTransaction($params)
    {
        if (isset($params['contactId'])) {
            $simulation = LightrailTransaction::simulate($params);
        } else {
//            $paramsWithShopperId = self::userSuppliedIdToShopperId($params);
            $simulation = LightrailTransaction::simulate($params);
        }

        return $simulation;

    }


    public static function createTransaction($params)
    {
        if (isset($params['contactId'])) {
            $transaction = LightrailTransaction::create($params);
        } else {
//            $paramsWithShopperId = self::userSuppliedIdToShopperId($params);
            $transaction = LightrailTransaction::create($params);
        }

        return $transaction;
    }


    // Helpers

    private static function userSuppliedIdToShopperId($params)
    {
        $newParams = $params;
        if (isset($newParams['userSuppliedId'])) {
            if (isset($newParams['shopperId']) && ($newParams['userSuppliedId'] != $newParams['shopperId'])) {
                throw new BadParameterException('Account creation error: cannot set different values for shopperId and userSuppliedId');
            } elseif ( ! isset($newParams['shopperId'])) {
                $newParams['shopperId'] = $newParams['userSuppliedId'];
            }
        }

        return $newParams;
    }

}
