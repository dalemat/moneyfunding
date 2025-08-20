<?php

namespace Funding\Requests\Api\Controller;

use Flarum\Api\Controller\AbstractShowController;
use Funding\Requests\Model\FundingRequest;
use Funding\Requests\Api\Serializer\FundingRequestSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class RejectFundingRequestController extends AbstractShowController
{
    public $serializer = FundingRequestSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $id = $request->getAttribute('id');
        $fundingRequest = FundingRequest::findOrFail($id);
        $fundingRequest->status = 'rejected';
        $fundingRequest->save();
        return $fundingRequest;
    }
}