<?php

namespace Funding\Wallet\Model;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class FundingRequest extends AbstractModel
{
    protected $table = 'funding_requests';

    protected $fillable = ['user_id', 'tx_hash', 'amount', 'status', 'reason'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
