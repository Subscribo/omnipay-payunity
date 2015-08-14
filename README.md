# Omnipay: PayUnity

**PayUnity driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/Subscribo/omnipay-payunity.svg)](https://travis-ci.org/Subscribo/omnipay-payunity)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements PayUnity support for Omnipay.

## Versions

Omnipay PayUnity driver version | PayUnity COPYandPAY version | Post Gateway
------------------------------- | --------------------------- | -----------------------------------------------
0.2.x                           | 4                           | No
0.3.x                           | 4                           | purchase() with token billing, refund(), void()
0.4.x                           | 4                           | also authorize() and capture()

## Installation

Omnipay PayUnity driver (beta version) is installed via [Composer](http://getcomposer.org/).
To install, add it to your `composer.json` file:

```json
{
    "require": {
        "subscribo/omnipay-payunity": "^0.4.1"
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
* [`PayUnity\Post`](#gateway-payunitypost) (from version 0.3.0)

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
* `description` alias for `presentationUsage`
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

* [`purchase()`](#method-purchase-and-authorize)
* [`authorize()`](#method-purchase-and-authorize)
* [`capture()`](#method-capture)
* [`void()`](#methods-void-and-refund)
* [`refund()`](#methods-void-and-refund)

#### Method `purchase()` and `authorize()`

Following parameters are needed (either as argument of `purchase()` / `authorize()` method
or set via setters on `PostPurchaseRequest` / `PostAuthorizeRequest` returned):

* `amount`
* `currency` (e.g. 'EUR')

In order to use tokens returned by getCardReference() from `CopyAndPayCompleteResponse`,
you need to provide them to `PostPurchaseRequest` either via parameter during `purchase(['cardReference' => '...']);` call
or using `setCardReference()` setter.

#### Method `capture()`

Required parameters:

* `amount`
* `currency`
* `transactionReference`

You may optionally specify transaction method ('CC', 'DD', ...), either directly via `setPaymentMethod()`
or indirectly, using reference stored in registration token via `setCardReference()` method.

For setting `transactionReference` and `cardReference` you may use also method [`fill()`](#method-fill)

#### Methods `void()` and `refund()`

Methods `void()` and `refund()` need parameter `transactionReference` set either via parameter, i.e. `void(['transactionReference' => '...']);`
or using `setTransactionReference()` method of `PostRefundRequest` or `PostVoidRequest`.
Its value may be obtained from transaction-to-be-voided/refunded response object using `getTransactionReference()` method.
(see [purchase.php](docs/example/Post/purchase.php) and [refund.php](docs/example/Post/refund.php) for code examples.)

You may optionally specify transaction method ('CC', 'DD', ...), either directly via `setPaymentMethod()`
or indirectly, using reference stored in registration token via `setCardReference()` method.

Method `refund()` needs also `amount` and `currency` parameters.

#### Post requests

Post gateway requests - GenericPostRequests and its subclasses (PostPurchaseRequest, PostRefundRequest, PostVoidRequest)
have these parameters (and corresponding setter getter methods):
* [Parameters inherited from Post gateway](#basic-usage)
* `transactionId` alias for `identificationTransactionId`
* `transactionReference` alias for `identificationReferenceId`
* `description` alias for `presentationUsage`
* `paymentMemo`
* `paymentCode`
* `paymentType`
* `paymentMethod` (inherited from common Omnipay interface)

And also other parameters inherited from common Omnipay interface,
such as `card`, `cardReference`, `amount`, `currency`...

Parameters `amount` and `currency` are required for PostPurchaseRequest and PostRefundRequest.

Parameter `cardReference` and/or `transactionReference` is required for proper processing of all Post gateway requests.

Payment code is computed using values from provided `paymentCode`, `paymentType`, `paymentMethod`, `cardReference`
parameters and default values. Default values and particular payment code computing algorithm
is specific to particular request class.

Besides methods inherited from common Omnipay interface (such as `send()` or `initialize()`),
Post gateway requests provide method `fill()` to make chaining requests easier.

##### Method `fill()`

Method `fill()` accepts two arguments:
* `$response` - and instance of `GenericPostResponse`
* `$fillMode` - optional, integer, specifying which parameters should be set. Available basic constants:
    * `GenericPostRequests::FILL_MODE_TRANSACTION_REFERENCE`
    * `GenericPostRequests::FILL_MODE_CARD_REFERENCE`
    * `GenericPostRequests::FILL_MODE_AMOUNT`
    * `GenericPostRequests::FILL_MODE_CURRENCY`
    * `GenericPostRequests::FILL_MODE_DESCRIPTION`

Constants could be combined using bit arithmetic. Available combination constants:
    * `GenericPostRequests::FILL_MODE_REFERENCES`
    * `GenericPostRequests::FILL_MODE_PRESENTATION`
    * `GenericPostRequests::FILL_MODE_ALL` currently alias for `GenericPostRequests::FILL_MODE_REFERENCES_AND_PRESENTATION`

Depending on `$fillMode`, `cardReference`, `transactionReference`, `amount`, `currency` and/or `description` parameters
are being set on request, on which this method has been used, using values from provided `$response` object.
If `$fillMode` is set to `true` (default), default fill mode for particular request class is used.
If the value provided evaluates as empty (or '0.00' for amount), the particular parameter is left intact.

#### Post and CopyAndPayCompletePurchase responses

Post gateway responses as well as `CopyAndPayCompletePurchase` response have also following methods:

* `getTransactionReference()` alias for `getIdentificationUniqueId()`
* `getTransactionId()` alias for `getIdentificationTransactionId()`
* `getAccountRegistration()`
* `getIdentificationShortId()`
* `getIdentificationShopperId()`
* `getPresentationAmount()`
* `getPresentationCurrency()`
* `getPresentationUsage()`
* `getProcessingReason()`
* `getProcessingReturn()`
* `getProcessingResult()`
* `getProcessingCode()`
* `getProcessingReasonCode()`
* `getProcessingReturnCode()`
* `getProcessingStatusCode()`
* `getProcessingPaymentCode()`
* `getTransactionResponse()`
* `getPostValidationErrorCode()`

Method `getCode()` tries to get 'PROCESSING.STATUS.CODE', either directly or by parsing 'PROCESSING.CODE',
if both fails, then it tries to provide error code from 'POST.VALIDATION'.

Method `getMessage()` tries to concatenate (with colon and spaces if both are provided) 'PROCESSING.REASON' and 'PROCESING.RESULT'.

Method `getCardReference()` returns tokens, which could be used for subsequent requests, via specifying `cardReference` option on purchase request.
These tokens are base64-encoded json, containing data from 'ACCOUNT.REGISTRATION' and 'PAYMENT.CODE'

Method `getPresentationAmount()` might return '0.00' instead of an empty / not specified value.

Methods `getPresentationAmount()`, `getPresentationCurrency()`,  `getPresentationUsage()` usually returns null for `CopyAndPayCompletePurchase`.

### Example code

For example code see:

* [COPYandPAY Purchase page](docs/example/COPYandPAY/purchase.php)
* [COPYandPAY Complete purchase page](docs/example/COPYandPAY/complete_purchase.php)
* [Post tokenized purchase page](docs/example/Post/purchase.php)
* [Post tokenized authorize page](docs/example/Post/authorize.php)
* [Post (partial) capture page](docs/example/Post/capture.php)
* [Post void page](docs/example/Post/void.php)
* [Post (partial) refund page](docs/example/Post/refund.php)

To run full workflow of example code you need to set up you routing mechanism to actually reach those examples,
provide your valid API credentials as environment variables
PAYUNITY_USER_LOGIN, PAYUNITY_USER_PWD, PAYUNITY_SECURITY_SENDER, PAYUNITY_TRANSACTION_CHANNEL and
PAYUNITY_DRIVER_FOR_OMNIPAY_EXAMPLES_URL_BASE containing url base to your examples.
(or you can provide these data by modifying your copy of example code itself)

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

For some online tests you might need to provide following environment variables, containing your API credentials:
PAYUNITY_USER_LOGIN, PAYUNITY_USER_PWD, PAYUNITY_SECURITY_SENDER, PAYUNITY_TRANSACTION_CHANNEL and
PAYUNITY_DRIVER_FOR_OMNIPAY_TESTING_ACCOUNT_REGISTRATION_REFERENCE containing registration reference string,
which you can create e.g. via using example code for COPYandPAY workflow
(with environment variables with the same API credentials).
It should be displayed as  Card Reference on [COPYandPAY Complete purchase page](docs/example/COPYandPAY/complete_purchase.php).

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
