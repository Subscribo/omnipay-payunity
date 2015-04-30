# Omnipay: PayUnity

**PayUnity driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/Subscribo/omnipay-payunity.svg)](https://travis-ci.org/Subscribo/omnipay-payunity)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements PayUnity support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, add it
to your `composer.json` file (you might need to add also development version of egeloen/http-adapter):

```json
{
    "require": {
        "subscribo/omnipay-payunity": "^0.2.0",
        "egeloen/http-adapter": "^0.8@dev"
    }
}
```

And run composer to update your dependencies:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update

## Basic Usage

The following gateways are provided by this package:

* PayUnity\COPYandPAY

Gateways in this package have following required options:

* securitySender
* transactionChannel
* userLogin
* userPwd

To get those please contact your PayUnity representative.

(Note: they are provided usually in the form 'SECURITY.SENDER' etc.)

Additionally these options could be specified:

* registrationMode
* transactionMode
* testMode
* identificationShopperId
* identificationInvoiceId
* identificationBulkId (Note: not sure of having any effect at the moment)

For meaning and possible values of transactionMode ('TRANSACTION.MODE') see PayUnity documentation.

For meaning of testMode see general [Omnipay documentation](https://thephpleague.com/omnipay)

Registration mode prepends 'RG.' to default transaction mode, thus making the request also registration request.
When transaction mode is specified, then registration mode is ignored (so you have to prepend 'RG.' manually, if you want to make the request registration)

### Usage of gateway PayUnity\COPYandPAY

Gateway PayUnity\COPYandPAY supports these request-sending methods:

* purchase()
* completePurchase()

#### purchase()

Method purchase() expects an array with this key as its argument:

* amount

Additionally these keys could be specified:

* currency (e.g. EUR)
* card
* cardReference
* identificationReferenceId
* brands
* returnUrl
* transactionId
* presentationUsage
* paymentMemo

Option brands could be an array or string with space separated list of (uppercase) brand identifiers, supported by COPYandPAY widget.
For supported brands see COPYandPAY documentation.

Option returnUrl should be an absolute url in your site, where user should be redirected after payment.

You need to provide brands and returnUrl either as part of purchase() argument, or when creating a widget later.

Method purchase() returns an instance of CopyAndPayPurchaseRequest having method send(), which in turn is sending the request and returning an instance of CopyAndPayPurchaseResponse having the following methods (additional to standard Omnipay RequestInterface methods and besides other helper and static methods):

* isTransactionToken()
* getTransactionToken()
* haveWidget()
* getWidget()
* getWidgetJavascript()
* getWidgetForm()

Method isSuccessful() always returns false, as the COPYandPAY workflow is as follows:

  1. using purchase() method you acquire transactionToken,
  2. then and you either manually, using static helpers
     or using CopyAndPayPurchaseResponse methods: getWidget()
     (or getWidgetJavascript() and getWidgetForm() if you want to have these parts separated)
     create the frontend widget and display it to customer
  3. and when customer fill and sends the widget,
  4. he is redirected to returnUrl provided,
  5. where you can finish/check the transaction (see below)

#### completePurchase()

Method completePurchase() could be called after customer had been redirected from widget (see above) back to your site.
It expects an array with key 'transactionToken' as a parameter,
however it could be invoked also with an empty array
and you can provide transaction token to returned instance of CopyAndPayCompletePurchaseRequest
via setTransactionToken($token) or fill(CopyAndPayPurchaseResponse $response) methods.
If transactionToken is not provided manually, an attempt will be made to retrieve it from httpRequest, provided to gateway constructor.
(That usually means, that if you do not specify transactionToken, it will be taken from url query of current page automatically.)

After transactionToken is provided to CopyAndPayCompletePurchaseRequest, you can call its send() method and receive CopyAndPayCompletePurchaseResponse, with following methods (additional to standard Omnipay RequestInterface methods):

* isWaiting() returns true when customer did not yet sent the widget form
* getIdentificationShortId()
* getIdentificationShopperId()
* getCardReference()

* getTransactionId() is alias for getIdentificationTransactionId()
* getTransactionReference() is alias for getIdentificationUniqueId()

getCardReference returns tokens, which could be used for subsequent requests, via specifying cardReference option on purchase request
(setCardReference is actually an alias for setIdentificationReferenceId, and getCardReference is an alias for getIdentificationReferenceId)

### Example code

For example code see:

* [Purchase page](docs/example/purchase.php)
* [Complete purchase page](docs/example/complete_purchase.php)

### General instructions

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

### Testing

For testing you need to install development dependencies:
```sh
    cd path/to/your/project
    cd vendor/subscribo/omnipay-payunity
    composer update
```

If you want to run both online and offline tests, run just phpunit.

If you want to run offline (not requiring internet connection) tests only, run:
```sh
    phpunit tests/offline
```

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release announcements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/thephpleague/omnipay-dummy/issues),
or better yet, fork the library and submit a pull request.
