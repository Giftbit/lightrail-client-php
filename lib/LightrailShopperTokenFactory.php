<?php

namespace Lightrail;


class LightrailShopperTokenFactory
{

    public static function generateForShopperId($shopperId, $validityInSeconds = 43200)
    {
        return LightrailShopperTokenFactory::generate( array('shi' => $shopperId), $validityInSeconds );
    }

    public static function generateForContactUserSuppliedId( $userSuppliedId, $validityInSeconds = 43200 )
    {
        return LightrailShopperTokenFactory::generate( array('cui' => $userSuppliedId), $validityInSeconds );
    }

    public static function generateForContactId($contactId, $validityInSeconds = 43200)
    {
        return LightrailShopperTokenFactory::generate( array('coi' => $contactId), $validityInSeconds );
    }

    private static function generate($g, $validityInSeconds = 43200)
    {
        if ( !isset(Lightrail::$apiKey)) {
            throw new BadParameterException("Lightrail.apiKey is not set.");
        }
        if ( !isset(Lightrail::$sharedSecret) ) {
            throw new BadParameterException('Lightrail.sharedSecret is not set.');
        }

        $payload = explode( '.', Lightrail::$apiKey );
        $payload = json_decode(base64_decode( $payload[1]), true );

        $iat = time();
        $token = array(
            'g' => array(
                'gui' => $payload['g']['gui'],
                'gmi' => $payload['g']['gmi']
            ) + $g,
            'iat' => $iat,
            'iss' => "MERCHANT"
        );

        if (isset($validityInSeconds)) {
            $exp = $iat + $validityInSeconds;
            $token['exp'] = $exp;
        }

        $jwt = \Firebase\JWT\JWT::encode( $token, Lightrail::$sharedSecret, 'HS256' );
        return $jwt;
    }
}
