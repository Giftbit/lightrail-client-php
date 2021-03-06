# Lightrail Client for PHP

This client is for Lightrail v1.  If you're not sure what that is you probably want the v2 library [lightrail-client-php-v2](https://github.com/Giftbit/lightrail-client-php-v2).


## Features

The following features are supported in this version:

- Account credits: create, retrieve, balance-check, and create/simulate transactions
- Contacts: create, retrieve, retrieve contact's account card

Note that the Lightrail API supports many other features and we are still working on covering them in this library. For a complete list of Lightrail API features check out the [Lightrail API documentation](https://www.lightrail.com/docs/).


## Usage

For a sample project using this library, check out the [Lightrail Stripe Sample PHP Web App](https://github.com/Giftbit/stripe-integration-sample-php-webapp).

### Configuration

Before using this client, you'll need to configure it to use your API key. You can find this in the Lightrail web app -- go to your [account settings](https://www.lightrail.com/app/#/account/profile), then click 'API keys' and 'Generate Key.'

```php
\Lightrail\Lightrail::$apiKey = <LIGHTRAIL_API_KEY>;
```


## Installation

### Composer

You can add this library as a dependency to your project using `composer`:
```
composer require lightrail/lightrail
```


## Use Case: Account Credits Powered by Lightrail

The remainder of this document is a quick demonstration of implementing account credits powered by Lightrail using this library. It will assume familiarity with the API and some core concepts, which are discussed in detail in the 'Powering Account Credits' section of the [API documentation](https://www.lightrail.com/docs/).

### Handling Contacts

#### Creating a New Contact

To create a new Contact, you need to provide a client-side unique identifier known as the `shopperId` or `userSuppliedId`. This is a per-endpoint unique identifier used to ensure idempotence (meaning that if the same request is issued more than once, it will not result in repeated actions). Typically this is a customer ID generated by your CRM. Note that the terms `shopperId` and `userSuppliedId` for Contacts are interchangeable (the Lightrail server will store the value as a `userSuppliedId` but you can refer to it as a `shopperId`).

Optionally, you can also provide an `email`, `firstName`, and `lastName`. Here is a sample call:

```php
$contactParams = array(
    'shopperId' => 'cust-a95a09',
    'email' => 'test@test.com'
);
$contact = \Lightrail\LightrailContact::create($params);
```

The return value will be a `LightrailContact` object, which will include both the `shopperId` you provided (as `userSuppliedId`) and a server-generated `contactId`. You can choose to save either value to retrieve the contact later:

```php
\Lightrail\LightrailContact: (
    'contactId' => 'contact-0s459jy6h56',
    'userSuppliedId' => 'cust-a95a09',
    'email' => 'test@test.com'
)
```

#### Retrieving a Contact

You can retrieve a Contact by its `shopperId` or its `contactId`. The response to this call will be a `LightrailContact` object similar to the one shown above.

```php
\Lightrail\LightrailContact::retrieveByContactId('contact-0s459jy6h56');

\Lightrail\LightrailContact::retrieveByShopperId('cust-a95a09');
```

### Handling Accounts

#### Creating Accounts

You can create an account for a contact based on their `shopperId` (identifier generated by your e-commerce system) or based on their `contactId` (generated by Lightrail). You must also specify the currency that the account will be in, and provide a`userSuppliedId`, a unique identifier from your own system. Since each Contact can have only up to one Account Card per currency, you can add the currency as a suffix to the `shopperId`/`userSuppliedId` you provided for the Contact.

You may optionally include an `initialValue` for the account. If provided, this must be a positive integer in the smallest currency unit (for example, `500` is 5 USD).

```php
$contact = \Lightrail\LightrailContact::retrieveByShopperId('cust-a95a09');

$accountParams = array(
    'shopperId'      => 'cust-a95a09',  // alternatively use the Lightrail generated identifier: 'contactId' => 'contact-0s459jy6h56'
    'userSuppliedId' => 'cust-a95a09-usd-account',
    'currency'       => 'USD',
    'initialValue'   => 500
);

$account = \Lightrail\LightrailAccount::createAccountCard();
```

The return value will be a `LightrailCard` object, which will include both the account card's `userSuppliedId` that you provided and a Lightrail-generated `cardId` which you can persist and use to retrieve the account card later, as well as several other details:

```php
\Lightrail\LightrailCard: (
    'cardId' => 'card-4358huf98r',
    'userSuppliedId' => 'cust-a95a09-usd-account',
    'currency' => 'USD',
    // output simplified for readability
)
```

#### Funding and Charging

To fund or charge an account, you can once again use either the `contactId` (generated by Lightrail) or the `shopperId` (generated by your e-commerce system) to identify the customer account that you wish to transact against.

You must additionally pass in the following:

- The `value` of the transaction: a positive `value` will add funds to the account, while a negative `value` will post a charge to the account. This amount must be in the smallest currency unit (e.g., `500` for 5 USD)
- The `currency` that the transaction is in (note that Lightrail does not handle currency conversion and the contact must have an account in the corresponding currency)
- A `userSuppliedId`, which is a unique transaction identifier to ensure idempotence (for example, the order ID from your e-commerce system)

```php
$transactionParams = array(
    'shopperId'      => 'cust-a95a09',
    'currency'       => 'USD',
    'value'          => -350,
    'userSuppliedId' => 'order-a90h09a509gaj00-a4'
);

\Lightrail\LightrailAccount::createTransaction($transactionParams);
```

The return value is a `LightrailTransaction` which includes the full details of the transaction, including both the `userSuppliedId` you provided and a server-generated `transactionId` you can later use to retrieve the transaction again:

```php
\Lightrail\LightrailTransaction: (
    'transactionId' => 'transaction-4358huf98r',
    'userSuppliedId' => 'order-a90h09a509gaj00-a4',
    'transactionType' => 'DRAWDOWN',
    'value' => -350,
    'currency' => 'USD',
    // output simplified for readability
)
```

#### Transaction Simulation and Balance Checking

Before attempting to post a transaction, you may wish to do a transaction simulation to find out whether or not the account has enough funds. In the case of insufficient funds, this can also tell you the maximum value for which the transaction _would be_ successful. For example, if you simulate a $35 drawdown Transaction, the method can tell you that it _would be_ successful if it were only for $20.

The parameters for this method call are almost identical to those for posting a transaction. To get the maximum value, add `nsf: false` to your transaction parameters:

```php
$simulationParams = array(
    'shopperId'      => 'cust-a95a09',
    'currency'       => 'USD',
    'value'          => -6500,
    'userSuppliedId' => 'order-a90h09a509gaj00-a4',
    'nsf'            => 'false'
);

\Lightrail\LightrailAccount::simulateTransaction($simulationParams);
```

The response will be similar to the following. Note that this is just a simulation and NOT an actual transaction; for instance, it does not have a `transactionId`. The response indicates that for this transaction, the maximum value this account can provide is $55.

```php
\Lightrail\LightrailTransaction: (
    'value' => -5500,
    'userSuppliedId' => 'order-a90h09a509gaj00-a4',
    'transactionType' => 'DRAWDOWN',
    'currency' => USD
    'transactionBreakdown' =>
        (
            (
                'value' => -500
                'valueAvailableAfterTransaction' => 0
                'valueStoreId' => value-497d783b68fc4b4caa4f12be19112fbd
            ),
            (
                'value' => -5000
                'valueAvailableAfterTransaction' => 0
                'valueStoreId' => value-4eee14b03d454a6aa514c87e1268639d
            )
        ),
    'transactionId' => null,
    // output simplified for readability
)
```

### Shopper Tokens

If you are using our [Drop-in Gift Card](https://www.lightrail.com/docs/#drop-in-gift-cards/quickstart) solution, you can use this library to generate shopper tokens for transacting against a customer's account. 

A shopper token is generated from a unique customer identifier from your system: this is the same `shopperId` or contact `userSuppliedId` you would have used when creating the Contact, or you can also use the Lightrail-generated `contactId` that comes back in the response when a Contact is created. 

```php
\Lightrail\LightrailShopperTokenFactory::generate(array('shopperId' => 'cust-a95a09'));
\Lightrail\LightrailShopperTokenFactory::generate(array('contactId' => 'contact-0s459jy6h56'));
```

You can also pass in an optional second argument specifying the token's validity in seconds: 

```php
\Lightrail\LightrailShopperTokenFactory::generate(array('shopperId' => 'cust-a95a09', 600));
```

Note that if you haven't yet created a Contact record, some functions that use the generated shopper token will create one for you automatically based solely on the `shopperId` you provide - ie Account creation. If you want extra information to be associated with the Contact, like their name or email address, you should [create the contact](#handling-contacts) first.


## Testing

**IMPORTANT: note that several environment variables are required for the tests to run.** After cloning the repo, `composer install` dependencies, then copy `.env.example` to `.env` and fill in the following (or use your preferred way of setting environment variables):

- ` LIGHTRAIL_API_KEY`: find this in to the Lightrail web app -- go to your [account settings](https://www.lightrail.com/app/#/account/profile), then click 'API keys' and 'Generate Key.' **Note** that for running tests, you should use a test mode key.

- `LIGHTRAIL_SHARED_SECRET`: set this to any string (used to generate shopper tokens).

- `CARD_ID`: this is the `cardId` for a USD card with at least a few dollars on it.

- `SHOPPER_ID`: the userSuppliedId for a Lightrail contact with a USD account with a few dollars in it.

- `CONTACT_ID`: the Lightrail-generated contactId for the same contact.


Then you can run `composer test`.


## Contributing

Bug reports and pull requests are welcome on GitHub at <https://github.com/Giftbit/lightrail-client-php>.


## Publishing

After pushing changes to Github, tag a new release. You can do this via the web interface or through the command line: 

```
git tag -a vX.X.X -m "Tag message or title"
git push origin vX.X.X
```

Then log into packagist.org and click "Update" on the `lightrail/lightrail` package (you must be logged in as the Lightrail user).


## License

This library is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).
