<?php

namespace CryptoFund\ERC20Money\Notifications;

use CryptoFund\ERC20Money\Models\ERC20Transaction;
use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;

class TransactionFailedBlueprint implements BlueprintInterface
{
    protected $transaction;
    
    public function __construct(ERC20Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
    
    public function getSubject()
    {
        return $this->transaction;
    }
    
    public function getFromUser()
    {
        return null;
    }
    
    public function getData()
    {
        return [
            'amount' => $this->transaction->amount,
            'txHash' => $this->transaction->tx_hash
        ];
    }
    
    public static function getType(): string
    {
        return 'erc20TransactionFailed';
    }
    
    public static function getSubjectModel(): string
    {
        return ERC20Transaction::class;
    }
}
