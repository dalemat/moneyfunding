<?php

namespace Funding\Requests\Model;

use Flarum\Database\AbstractModel;

class FundingRequest extends AbstractModel
{
    protected $table = 'funding_requests';
    protected $fillable = ['user_id', 'amount', 'tx_hash', 'status'];
}