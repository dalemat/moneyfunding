<?php

namespace CryptoFund\ERC20Money\Api\Controllers;

use CryptoFund\ERC20Money\Models\ERC20Transaction;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\Exception\RouteNotFoundException;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ShowTransactionController extends AbstractShowController
{
    public $serializer = 'CryptoFund\ERC20Money\Api\Serializers\ERC20TransactionSerializer';
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertRegistered();
        
        $id = Arr::get($request->getQueryParams(), 'id');
        
        $transaction = ERC20Transaction::where('id', $id)
            ->where('user_id', $actor->id)
            ->first();
            
        if (!$transaction) {
            throw new RouteNotFoundException();
        }
        
        return $transaction;
    }
}
