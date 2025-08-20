<?php

namespace Funding\Requests\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;

class FundingRequestSerializer extends AbstractSerializer
{
    protected $type = 'funding-requests';

    protected function getDefaultAttributes($fundingRequest)
    {
        return [
            'id' => $fundingRequest->id,
            'user_id' => $fundingRequest->user_id,
            'amount' => $fundingRequest->amount,
            'tx_hash' => $fundingRequest->tx_hash,
            'status' => $fundingRequest->status,
            'created_at' => $fundingRequest->created_at,
            'updated_at' => $fundingRequest->updated_at,
        ];
    }
}