<?php

namespace Funding\Wallet\Listener;

use Funding\Wallet\Model\FundingRequest;
use Funding\Wallet\Notification\FundingRequestApprovedBlueprint;
use Funding\Wallet\Notification\FundingRequestRejectedBlueprint;
use Flarum\Notification\NotificationSyncer;

class SendFundingRequestNotifications
{
    protected $notifications;

    public function __construct(NotificationSyncer $notifications)
    {
        $this->notifications = $notifications;
    }

    public function handle(FundingRequest $funding)
    {
        if ($funding->status === 'approved') {
            $this->notifications->sync(
                new FundingRequestApprovedBlueprint($funding),
                [$funding->user]
            );
        }

        if ($funding->status === 'rejected') {
            $this->notifications->sync(
                new FundingRequestRejectedBlueprint($funding),
                [$funding->user]
            );
        }
    }
}
