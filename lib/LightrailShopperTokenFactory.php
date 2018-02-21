<?php

namespace Lightrail;

class LightrailShopperTokenFactory
{
    public static function generate($contact, $validityInSeconds = 43200)
    {
        if ( ! isset(Lightrail::$apiKey) || empty(Lightrail::$apiKey)) {
            throw new Exceptions\BadParameterException("Lightrail.apiKey is empty or not set.");
        }
        if (!isset(Lightrail::$sharedSecret)) {
            throw new Exceptions\BadParameterException('Lightrail.sharedSecret is not set.');
        }

        if (isset($contact['shopperId'])) {
            $g = array('shi' => $contact['shopperId']);
        } elseif (isset($contact['contactId'])) {
            $g = array('coi' => $contact['contactId']);
        } elseif (isset($contact['userSuppliedId'])) {
            $g = array('cui' => $contact['userSuppliedId']);
        } else {
            throw new Exceptions\BadParameterException("contact must set one of: shopperId, contactId, userSuppliedId");
        }

        $payload = explode('.', Lightrail::$apiKey);
        $payload = json_decode(base64_decode($payload[1]), true);

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

        $jwt = \Firebase\JWT\JWT::encode($token, Lightrail::$sharedSecret, 'HS256');
        return $jwt;
    }
}
