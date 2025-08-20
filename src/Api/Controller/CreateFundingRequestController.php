<?php

namespace Funding\Requests\Api\Controller;

use Flarum\Api\Controller\AbstractCreateController;
use Funding\Requests\Model\FundingRequest;
use Funding\Requests\Api\Serializer\FundingRequestSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateFundingRequestController extends AbstractCreateController
{
    public $serializer = FundingRequestSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        $data = $request->getParsedBody();

        return FundingRequest::create([
            'user_id' => $actor->id,
            'amount' => $data['amount'] ?? 0,
            'tx_hash' => $data['tx_hash'] ?? '',
            'status' => 'pending',
        ]);
    }
}