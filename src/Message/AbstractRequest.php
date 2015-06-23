<?php namespace Omnipay\PayUnity\Message;

use Subscribo\Omnipay\Shared\Message\AbstractRequest as Base;
use Subscribo\Omnipay\Shared\Traits\SimpleRestfulRequestTrait;

/**
 * Abstract Class AbstractRequest
 *
 * @package Omnipay\PayUnity
 */
abstract class AbstractRequest extends Base
{
    use SimpleRestfulRequestTrait;
}
