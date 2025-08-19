<?php

namespace Funding\Wallet\Policy;

use Flarum\User\User;
use Funding\Wallet\Model\FundingRequest;

class FundingRequestPolicy
{
    public function viewAny(User $actor)
    {
        return $actor->isAdmin();
    }

    public function view(User $actor, FundingRequest $request)
    {
        return $actor->isAdmin() || $actor->id === (int) $request->user_id;
    }
}
