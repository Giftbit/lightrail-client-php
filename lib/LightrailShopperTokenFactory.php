<?php

namespace Lightrail;

class LightrailShopperTokenFactory
{
    /**
     * Generate a shopper token that can be used to make Lightrail calls
     * restricted to that particular shopper.  The shopper can be defined by the
     * contactId, userSuppliedId, or shopperId.
     *
     * eg: `generateShopperToken(array("shopperId" => "user-12345"));`
     * eg: `generateShopperToken(array("shopperId" => "user-12345"), array("validityInSeconds" => 43200, "metadata" => array("foo" => "bar")));`
     *
     * @param $contact array an associative array that defines one of: contactId, userSuppliedId or shopperId
     * @param $options array an associative array that may define: `validityInSeconds` the number of seconds the shopper token will be valid for,
     *              `metadata` additional data that can be signed in the shopper token.
     * @return string the shopper token
     */
    public static function generate($contact, $options = array())
    {
        if (!isset(Lightrail::$apiKey) || empty(Lightrail::$apiKey)) {
            throw new Exceptions\BadParameterException("Lightrail.apiKey is empty or not set.");
        }
        if (!isset(Lightrail::$sharedSecret) || empty(Lightrail::$sharedSecret)) {
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

        $validityInSeconds = 43200;
        $metadata = null;
        if (is_numeric($options)) {
            // Support for legacy code when the second param was validityInSeconds.
            $validityInSeconds = $options;
        } elseif (is_array($options)) {
            if (isset($options['validityInSeconds'])) {
                $validityInSeconds = $options['validityInSeconds'];
            }
            if (isset($options['metadata'])) {
                $metadata = $options['metadata'];
            }
        }

        if ($validityInSeconds <= 0) {
            throw new Exceptions\BadParameterException("validityInSeconds must be > 0");
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
            'exp' =>  $iat + $validityInSeconds,
            'iss' => "MERCHANT",
            'roles' => ['shopper']
        );
        if (!is_null($metadata)) {
            $token['metadata'] = $metadata;
        }

        $jwt = \Firebase\JWT\JWT::encode($token, Lightrail::$sharedSecret, 'HS256');
        return $jwt;
    }
}
