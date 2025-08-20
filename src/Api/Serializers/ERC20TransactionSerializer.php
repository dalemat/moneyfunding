<?php

namespace CryptoFund\ERC20Money\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;
use Flarum\Api\Serializer\UserSerializer;

class ERC20TransactionSerializer extends AbstractSerializer
{
    protected $type = 'erc20-transactions';

    protected function getDefaultAttributes($transaction)
    {
        return [
            'id' => $transaction->id,
            'txHash' => $transaction->tx_hash,
            'amount' => (float) $transaction->amount,
            'points' => (float) $transaction->points,
            'status' => $transaction->status,
            'contractAddress' => $transaction->contract_address,
            'blockNumber' => $transaction->block_number,
            'confirmationCount' => $transaction->confirmation_count,
            'createdAt' => $transaction->created_at,
            'updatedAt' => $transaction->updated_at,
        ];
    }

    protected function user($transaction)
    {
        return $this->hasOne($transaction, UserSerializer::class);
    }
}
