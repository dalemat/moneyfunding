<?php

namespace CryptoFund\ERC20Money\Models;

use Flarum\Database\AbstractModel;
use Flarum\User\User;

class ERC20Transaction extends AbstractModel
{
    protected $table = 'erc20_transactions';
    
    protected $fillable = [
        'user_id',
        'tx_hash',
        'amount',
        'points',
        'status',
        'contract_address',
        'block_number',
        'confirmation_count'
    ];

    protected $casts = [
        'amount' => 'decimal:18',
        'points' => 'decimal:2',
        'block_number' => 'integer',
        'confirmation_count' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
