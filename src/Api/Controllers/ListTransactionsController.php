<?php

namespace CryptoFund\ERC20Money\Api\Controllers;

use CryptoFund\ERC20Money\Models\ERC20Transaction;
use Flarum\Api\Controller\AbstractListController;
use Flarum\Http\RequestUtil;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ListTransactionsController extends AbstractListController
{
    public $serializer = '\CryptoFund\ERC20Money\Api\Serializers\ERC20TransactionSerializer';

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertRegistered();

        return ERC20Transaction::where('user_id', $actor->id)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();
    }
}
