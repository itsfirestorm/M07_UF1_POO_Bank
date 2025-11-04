<?php
namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:24 PM
 */

use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Exceptions\BaseExceptions;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;

abstract class BaseTransaction
{
    use AmountValidationTrait;
    protected $amount;
    public function __construct($amount)
    {
        try {
            $this->validateAmount($amount);
        } catch (ZeroAmountException $e) {
            throw new ZeroAmountException($e->getMessage());
        }
        try {
            $this->validateAmount($amount);
        } catch (InvalidArgsException $e) {
            throw new InvalidArgsException($e->getMessage());
        }
        $this->amount = $amount;
    }
}
