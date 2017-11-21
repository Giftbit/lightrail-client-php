<?php

namespace Lightrail;
class TestConfig {
	public static $apiKey = ''; //  Get test credentials from https://www.lightrail.com/app/#/account/
	public static $lightrailSharedSecret = '';

	public static $contactId = ''; //these two should correspond. the contact must have a 'USD' card with a non-zero balance.
	public static $shopperId = '';
}
