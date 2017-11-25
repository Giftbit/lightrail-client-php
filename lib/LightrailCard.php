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
}
