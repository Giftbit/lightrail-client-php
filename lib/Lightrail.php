<?php

namespace Lightrail;

class Lightrail {
	public static $apiKey;
	public static $sharedSecret;

	static $API_BASE = 'https://api.lightrail.com/v1/';

	public static function setSharedSecret( $theSharedSecret ) {
		self::$sharedSecret = $theSharedSecret;
	}

	public static function setApiKey( $theApiKey ) {
		self::$apiKey = $theApiKey;
	}

	public static function checkApiKey() {
		if ( ! isset( self::$apiKey ) ) {
			throw new BadParameterException( 'Lightrail::$apiKey not set.' );
		}
	}

	public static function checkCardParams( $params ) {
		self::checkApiKey();
		if ( ! isset( $params['userSuppliedId'] ) ) {
			throw new BadParameterException( 'Card userSuppliedId not set.' );
		}
		if ( ! isset( $params['currency'] ) ) {
			throw new BadParameterException( 'Card currency not set.' );
		}
	}

	public static function checkAccountCardParamsByContactId( $params ) {
		self::checkApiKey();

		if ( ! isset( $params['userSuppliedId'] ) ) {
			throw new BadParameterException( 'Card userSuppliedId not set.' );
		}
		if ( ! isset( $params['currency'] ) ) {
			throw new BadParameterException( 'Card currency not set.' );
		}
		if ( ! isset( $params['contactId'] ) ) {
			throw new BadParameterException( 'Contact ID not set.' );
		}
	}

	public static function checkAccountCardParamsByShopperId( $params ) {
		self::checkApiKey();

		if ( ! isset( $params['userSuppliedId'] ) ) {
			throw new BadParameterException( 'Card userSuppliedId not set.' );
		}
		if ( ! isset( $params['currency'] ) ) {
			throw new BadParameterException( 'Card currency not set.' );
		}
		if ( ! isset( $params['shopperId'] ) ) {
			throw new BadParameterException( 'Shopper ID not set.' );
		}
	}

}