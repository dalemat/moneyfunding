<?php

namespace Funding\Requests\Api\Controller;

use Flarum\Api\Controller\AbstractListController;
use Funding\Requests\Model\FundingRequest;
use Funding\Requests\Api\Serializer\FundingRequestSerializer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListFundingRequestsController extends AbstractListController
{
    public $serializer = FundingRequestSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        return FundingRequest::all();
    }
}