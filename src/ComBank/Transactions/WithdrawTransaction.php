<?php
namespace ComBank\Transactions;

/**
 * Created by VS Code.
 * User: JPortugal
 * Date: 7/28/24
 * Time: 1:22 PM
 */

use ComBank\Bank\Contracts\BankAccountInterface;
use ComBank\Exceptions\InvalidOverdraftFundsException;
use ComBank\Exceptions\ZeroAmountException;
use ComBank\Transactions\Contracts\BankTransactionInterface;

class WithdrawTransaction extends BaseTransaction implements BankTransactionInterface
{
    public function applyTransaction(BankAccountInterface $account): float
    {
        if ($this->getAmount() <= 0) {
            throw new ZeroAmountException("You specified an amount of zero.");
        }
        $newBalance = $account->getBalance() - $this->getAmount();
        if (!$account->getOverdraft()->isGrantOverdraftFunds($newBalance)) {
            throw new InvalidOverdraftFundsException('Your withdraw has reached the max overdraft funds.');
        }
        return $newBalance;
    }

    public function getTransactionInfo(): string
    {
        return "WITHDRAW_TRANSACTION";
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
