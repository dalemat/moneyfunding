<?php

namespace Funding\Wallet\Api\Controller;

use Funding\Wallet\Api\Serializer\FundingRequestSerializer;
use Funding\Wallet\Model\FundingRequest;
use Flarum\Api\Controller\AbstractListController;
use Flarum\User\Exception\NotAuthenticatedException;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListFundingRequestsController extends AbstractListController
{
    public $serializer = FundingRequestSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        if (!$actor || !$actor->isActivated()) {
            throw new NotAuthenticatedException();
        }

        if ($actor->isAdmin()) {
            return FundingRequest::query()->with('user')->orderByDesc('created_at')->get();
        }

        return FundingRequest::query()
            ->where('user_id', $actor->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }
}
