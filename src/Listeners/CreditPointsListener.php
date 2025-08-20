<?php

namespace CryptoFund\ERC20Money\Listeners;

use CryptoFund\ERC20Money\Notifications\PointsCreditedBlueprint;
use Flarum\Notification\NotificationSyncer;
use Flarum\User\Event\Saving;

class CreditPointsListener
{
    protected $notifications;

    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(Saving $event)
    {
        $user = $event->user;
        
        // Check if erc20_balance was updated and increased
        if ($user->isDirty('erc20_balance')) {
            $oldBalance = $user->getOriginal('erc20_balance') ?? 0;
            $newBalance = $user->erc20_balance ?? 0;
            
            if ($newBalance > $oldBalance) {
                $creditedAmount = $newBalance - $oldBalance;
                
                // Send notification
                $this->notifications->sync(
                    new PointsCreditedBlueprint($creditedAmount),
                    [$user]
                );
            }
        }
    }
}
