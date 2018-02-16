<?php

namespace Lightrail;

class LightrailProgram extends LightrailObject
{
    private static $JSON_ROOT_NAME = 'program';

    private static $PROGRAMS_ENDPOINT = 'programs';

    public static function create($params)
    {
        Lightrail::checkProgramParams($params);
        $endpoint = Lightrail::$API_BASE . self::$PROGRAMS_ENDPOINT;
        $response = json_decode(LightrailAPICall::post($endpoint, $params), true);

        return new LightrailProgram($response, self::$JSON_ROOT_NAME);
    }
}
