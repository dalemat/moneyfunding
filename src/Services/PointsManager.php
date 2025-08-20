<?php

namespace CryptoFund\ERC20Money\Services;

use Flarum\User\User;
use AntoineFr\Money\Event\MoneyUpdated;
use Illuminate\Contracts\Events\Dispatcher;

class PointsManager
{
    protected $events;
    
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }
    
    public function creditPoints(User $user, int $points): void
    {
        $oldMoney = $user->money;
        $user->money = ($user->money ?? 0) + $points;
        $user->save();
        
        // Dispatch money updated event
        $this->events->dispatch(new MoneyUpdated($user, $oldMoney, $user->money));
    }
    
    public function debitPoints(User $user, int $points): bool
    {
        if (($user->money ?? 0) < $points) {
            return false;
        }
        
        $oldMoney = $user->money;
        $user->money = $user->money - $points;
        $user->save();
        
        // Dispatch money updated event
        $this->events->dispatch(new MoneyUpdated($user, $oldMoney, $user->money));
        
        return true;
    }
    
    public function getBalance(User $user): int
    {
        return $user->money ?? 0;
    }
}
