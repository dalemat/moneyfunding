<?php

namespace Funding\Wallet\Api\Serializer;

use Flarum\Api\Serializer\AbstractSerializer;
use Funding\Wallet\Model\FundingRequest;

class FundingRequestSerializer extends AbstractSerializer
{
    protected $type = 'funding-requests';

    /**
     * @param FundingRequest $request
     */
    protected function getDefaultAttributes($request)
    {
        return [
            'id' => (int) $request->id,
            'userId' => (int) $request->user_id,
            'txHash' => (string) $request->tx_hash,
            'amount' => (string) $request->amount,
            'status' => (string) $request->status,
            'reason' => $request->reason,
            'createdAt' => $this->formatDate($request->created_at),
            'updatedAt' => $this->formatDate($request->updated_at),
        ];
    }
}
