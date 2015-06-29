# Omnipay: PayUnity

**PayUnity driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/Subscribo/omnipay-payunity.svg)](https://travis-ci.org/Subscribo/omnipay-payunity)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements PayUnity support for Omnipay.

## Versions

Omnipay PayUnity driver version | PayUnity COPYandPAY version | Post Gateway
------------------------------- | --------------------------- | --------------------
0.2.x                           | 4                           | No
0.3.x                           | 4                           | purchase() with token billing, refund(), void()

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, add it
to your `composer.json` file (you might need to add also development version of egeloen/http-adapter):

for alpha version:
```json
{
    "require": {
        "subscribo/omnipay-payunity": "0.2.2",
        "egeloen/http-adapter": "^0.8@dev"
    }
}
```

for development (less stable) version:
```json
{
    "require": {
        "subscribo/omnipay-payunity": "0.3.*@dev",
        "egeloen/http-adapter": "^0.8@dev"
    }
}
```

and run composer to update your dependencies:
```sh
    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update
```

## Basic Usage

The following gateways are provided by this package:

* [`PayUnity\COPYandPAY`](#gateway-payunitycopyandpay)
* [`PayUnity\Post`](#gateway-payunitypost) (from version 0.3.x)

Gateways in this package have following required options:

* `securitySender`
* `transactionChannel`
* `userLogin`
* `userPwd`

To get those please contact your PayUnity representative.

(Note: they are provided usually in the form 'SECURITY.SENDER' etc.)

Additionally these options could be specified:

* `registrationMode`
* `transactionMode`
* `testMode`
* `identificationShopperId`
* `identificationInvoiceId`
* `identificationBulkId` (Note: not sure of having any effect at the moment)

For meaning and possible values of `transactionMode` ('TRANSACTION.MODE') see PayUnity documentation.

For meaning of `testMode` see general [Omnipay documentation](https://thephpleague.com/omnipay)

Setting `registrationMode` to `true` prepends `RG.` to default transaction mode, thus making the request also registration request.
When `transactionMode` is specified, then registration mode is ignored (so you have to prepend 'RG.' manually, if you want to make the request registration)

### Gateway `PayUnity\COPYandPAY`

Gateway `PayUnity\COPYandPAY` supports these request-sending methods:

* [`purchase()`](#method-purchase)
* [`completePurchase()`](#method-completepurchase)

#### Method `purchase()`

Method `purchase()` expects an array with these keys as its argument:

* `amount`
* `currency` (e.g. EUR)

Additionally these keys could be specified:

* `card` (using CreditCard object with extended list of attributes, including mobile, salutation, identificationDocumentNumber and identificationDocumentType)
* `cardReference` (result of [getCardReference()](#post-and-copyandpaycompletepurchase-responses) from a previous call with registration
* `identificationReferenceId`
* `brands`
* `returnUrl`
* `transactionId`
* `presentationUsage`
* `paymentMemo`

Option `brands` could be an array or string with space separated list of (uppercase) brand identifiers, supported by COPYandPAY widget.
For supported brands see COPYandPAY documentation.

Option `returnUrl` should be an absolute url in your site, where user should be redirected after payment.

You need to provide `brands` and `returnUrl` either as part of `purchase()` argument, or when creating a widget later.

Method `purchase()` returns an instance of `CopyAndPayPurchaseRequest` having method `send()`,
which in turn is sending the request and returning an instance of `CopyAndPayPurchaseResponse` having the following methods
(additional to standard Omnipay `RequestInterface` methods and besides other helper and static methods):

* `isTransactionToken()`
* `getTransactionToken()`
* `haveWidget()`
* `getWidget()`

Method `isSuccessful()` always returns false, as the COPYandPAY workflow is as follows:

  1. using `purchase()` method you acquire `transactionToken`,
  2. then create or get (using `CopyAndPayPurchaseResponse::getWidget()`)
     frontend widget and display it to customer (you can echo it or render it by parts, see CopyAndPayWidget class for more details)
  3. and when customer fills and sends the widget,
  4. he is redirected to `returnUrl` provided,
  5. where you can finish/check the transaction (see below)

#### Class `CopyAndPayWidget`

Class constructor, methods `render()`, `isRenderable()`, `renderHtmlForm()`, `renderJavascript()`
and `CopyAndPayPurchaseResponse::getWidget()` accepts as first (optional) argument array with following keys:

* `transactionToken`
* `testMode` (optional; `false` or `true`)
* `returnUrl`
* `brands`
* `language` (optional; 2 character lowercase string - language descriptor, e.g. 'en', 'de'...)
* `style` (optional; 'card', 'plain' or 'none');
* `loadCompressedJavascript` (optional; `true` or `false`)
* `loadJavascriptAsynchronously` (optional; `true` or `false`)

First two parameters are usually provided to the constructor via `CopyAndPayPurchaseResponse::getWidget()`,
if `returnUrl` and/or `brands` had been set on `purchase()` method, these are provided as well,
otherwise they should be provided manually either through setters of `CopyAndPayWidget` object or via `$parameters` argument on rendering.

##### Methods:

* `render()` - render complete widget
* `renderHtmlForm()` - render html part - you can use it on place, where you want the form to be rendered
* `renderJavascript()` - render javascript loading part, you can put in e.g. in html head
* `isRenderable()` - returns true, if widget can be rendered with parameters provided (if any)
* `__toString()` - is used, when echoing the widget, returns empty string for non-renderable widget
* `getParameters()`
* `getDefaultParameters()`
* getters and setter for particular parameters

#### Method `completePurchase()`

Method `completePurchase()` could be called after customer had been redirected from widget (see above) back to your site.
It expects an array with key 'transactionToken' as a parameter,
however it could be invoked also with an empty array
and you can provide transaction token to returned instance of `CopyAndPayCompletePurchaseRequest`
via `setTransactionToken($token)` or `fill(CopyAndPayPurchaseResponse $response)` methods.
If `transactionToken` is not provided manually, an attempt will be made to retrieve it from httpRequest, provided to gateway constructor.
(That usually means, that if you do not specify `transactionToken`, it will be taken from url query of current page automatically.)

After `transactionToken` is provided to `CopyAndPayCompletePurchaseRequest`, you can call its `send()` method
and receive `CopyAndPayCompletePurchaseResponse`.

Method `CopyAndPayCompletePurchaseResponse::isWaiting()` returns true when customer did not yet sent the widget form.
For other methods see [Post and CopyAndPayCompletePurchase responses](#post-and-copyandpaycompletepurchase-responses)

### Gateway `PayUnity\Post`

Gateway `PayUnity\Post` contains following methods:

* [`purchase()`](#method-purchase-1)
* [`void()`](#methods-void-and-refund)
* [`refund()`](#methods-void-and-refund)

#### Method `purchase()`

Following parameters are needed (either as argument of `purchase()` method or set via setters on `PostPurchaseRequest` returned:

* `amount`
* `currency` (e.g. 'EUR')

In order to use tokens returned by getCardReference() from `CopyAndPayCompleteResponse`,
you need to provide them to `PostPurchaseRequest` either via parameter during `purchase(['cardReference' => '...']);` call
or using `setCardReference()` setter.

#### Methods `void()` and `refund()`

Methods `void()` and `refund()` need parameter `transactionReference` set either via parameter, i.e. `void(['transactionReference' => '...']);`
or using `setTransactionReference()` method of `PostRefundRequest` or `PostVoidRequest`.
Its value may be obtained from transaction-to-be-voided/refunded response object using `getTransactionReference()` method.
(see [purchase.php](docs/example/Post/purchase.php) and [refund.php](docs/example/Post/refund.php) for code examples.)

You may optionally specify transaction method ('CC', 'DD', ...), either directly via `setPaymentMethod()`
or indirectly, using reference stored in registration token via `setCardReference()` method.

Method `refund()` needs also `amount` and `currency` parameters.

#### Post and CopyAndPayCompletePurchase responses

Post gateway responses as well as `CopyAndPayCompletePurchase` response have also following methods:

* `getTransactionReference()` alias for `getIdentificationUniqueId()`
* `getTransactionId()` alias for `getIdentificationTransactionId()`
* `getAccountRegistration()`
* `getIdentificationShortId()`
* `getIdentificationShopperId()`
* `getProcessingReason()`
* `getProcessingReturn()`
* `getProcessingResult()`
* `getProcessingCode()`
* `getProcessingReasonCode()`
* `getProcessingReturnCode()`
* `getProcessingResultCode()`
* `getProcessingStatusCode()`
* `getProcessingPaymentCode()`
* `getPostValidationErrorCode()`

Method `getCode()` tries to get 'PROCESSING.STATUS.CODE', either directly or by parsing 'PROCESSING.CODE',
if both fails, then it tries to provide error code from 'POST.VALIDATION'.

Method `getMessage()` tries to concatenate (with colon and spaces if both are provided) 'PROCESSING.REASON' and 'PROCESING.RESULT'.

Method `getCardReference()` returns tokens, which could be used for subsequent requests, via specifying `cardReference` option on purchase request.
These tokens are base64-encoded json, containing data from 'ACCOUNT.REGISTRATION' and 'PAYMENT.CODE'

### Example code

For example code see:

* [COPYandPAY Purchase page](docs/example/COPYandPAY/purchase.php)
* [COPYandPAY Complete purchase page](docs/example/COPYandPAY/complete_purchase.php)
* [Post tokenized purchase page](docs/example/Post/purchase.php)
* [Post void page](docs/example/Post/void.php)
* [Post refund page](docs/example/Post/refund.php)


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

### General

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release announcements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

### Omnipay PayUnity driver specific

If you believe you have found a bug, please send us an e-mail (packages@subscribo.io)
or report it using the [GitHub issue tracker](https://github.com/Subscribo/omnipay-payunity/issues),
or better yet, fork the library and submit a pull request.

### Links

* PayUnity web page: http://www.payunity.com
* PayUnity COPYandPAY version 4 documentation: https://payunity.3cint.com/flex/
* Omnipay Library web page: http://omnipay.thephpleague.com
* Omnipay Library Github Project: https://github.com/thephpleague/omnipay
