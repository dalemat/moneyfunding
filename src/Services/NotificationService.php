<?php

namespace CryptoFund\ERC20Money\Services;

use CryptoFund\ERC20Money\Models\ERC20Transaction;
use Flarum\Notification\NotificationSyncer;
use CryptoFund\ERC20Money\Notifications\PointsCreditedBlueprint;
use CryptoFund\ERC20Money\Notifications\TransactionFailedBlueprint;

class NotificationService
{
    protected $notifications;
    
    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }
    
    public function notifyPointsCredited(ERC20Transaction $transaction): void
    {
        $this->notifications->sync(
            new PointsCreditedBlueprint($transaction),
            [$transaction->user]
        );
    }
    
    public function notifyTransactionFailed(ERC20Transaction $transaction): void
    {
        $this->notifications->sync(
            new TransactionFailedBlueprint($transaction),
            [$transaction->user]
        );
    }
}
