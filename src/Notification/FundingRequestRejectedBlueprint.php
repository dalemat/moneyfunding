<?php

namespace Funding\Wallet\Notification;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\User\User;
use Funding\Wallet\Model\FundingRequest;

class FundingRequestRejectedBlueprint implements BlueprintInterface
{
    public $request;

    public function __construct(FundingRequest $request)
    {
        $this->request = $request;
    }

    public function getFromUser(): ?User
    {
        return null;
    }

    public function getSubject()
    {
        return $this->request;
    }

    public function getData(): array
    {
        return [
            'txHash' => $this->request->tx_hash,
            'amount' => (string)$this->request->amount,
            'reason' => $this->request->reason,
        ];
    }

    public static function getType(): string
    {
        return 'fundingRequestRejected';
    }

    public static function getSubjectModel(): string
    {
        return FundingRequest::class;
    }
}
