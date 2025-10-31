<?php
namespace ComBank\Bank;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/27/24
 * Time: 7:25 PM
 */

use ComBank\Exceptions\BankAccountException;
use ComBank\Exceptions\InvalidArgsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\OverdraftStrategy\NoOverdraft;
use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\FailedTransactionException;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\OverdraftStrategy\Contracts\OverdraftInterface;
use ComBank\OverdraftStrategy\SilverOverdraft;
use ComBank\Support\Traits\AmountValidationTrait;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class BankAccount implements BankAccountInterface
{
    private $balance;
    private $status;
    private $overdraft;

    public function __construct(float $newBalance = 0.0)
    {
        $this->balance = $newBalance;
        $this->status = BankAccountInterface::STATUS_OPEN;
    }

    public function transaction(BankTransactionInterface $bankTransaction): FailedTransactionException|null
    {
        if (!$this->isOpen()) {
            return new FailedTransactionException("Your account should be open.");
        }
        try {
            $this->setBalance($bankTransaction->applyTransaction($this));
        } catch (InvalidOverdraftFundsException $e) {
            throw new FailedTransactionException($e->getMessage());
        }
        return null;
    }

    public function isOpen(): bool
    {
        if ($this->status === BankAccountInterface::STATUS_OPEN) {
            return true;
        } else {
            return false;
        }
    }

    public function reopenAccount(): void
    {
        if ($this->status === BankAccountInterface::STATUS_CLOSED) {
            $this->status = BankAccountInterface::STATUS_OPEN;
        } else {
            echo "Your account is already open!";
        }
    }

    public function closeAccount(): void
    {
        if ($this->status === BankAccountInterface::STATUS_OPEN) {
            $this->status = BankAccountInterface::STATUS_CLOSED;
        } else {
            echo "Your account is already closed!";
        }
    }

    public function getBalance(): float
    {
        return $this->balance;
    }

    public function getOverdraft(): OverdraftInterface
    {
        return $this->overdraft;
    }

    public function applyOverdraft(OverdraftInterface $selectedOverdraft): void
    {
        $this->overdraft = $selectedOverdraft;
    }

    public function setBalance(float $newBalance): void
    {
        $this->balance = $newBalance;
    }
}
