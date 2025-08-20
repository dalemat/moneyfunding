<?php

namespace Funding\Wallet\Api\Controller;

use Funding\Wallet\Api\Serializer\FundingRequestSerializer;
use Funding\Wallet\Model\FundingRequest;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\User\Exception\NotAuthenticatedException;
use Illuminate\Validation\ValidationException;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class CreateFundingRequestController extends AbstractCreateController
{
    public $serializer = FundingRequestSerializer::class;

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        if (!$actor || !$actor->isActivated()) {
            throw new NotAuthenticatedException();
        }

        $payload = (array) $request->getParsedBody();
        $tx = isset($payload['tx_hash']) ? trim((string)$payload['tx_hash']) : '';
        $amount = isset($payload['amount']) ? (string)$payload['amount'] : '';

        if ($tx === '' || !preg_match('/^0x[0-9a-fA-F]{64}$/', $tx)) {
            throw ValidationException::withMessages(['tx_hash' => 'Invalid transaction hash']);
        }
        if ($amount === '' || !preg_match('/^\d+(?:\.\d+)?$/', $amount)) {
            throw ValidationException::withMessages(['amount' => 'Invalid amount']);
        }

        if (FundingRequest::query()->where('tx_hash', $tx)->exists()) {
            throw ValidationException::withMessages(['tx_hash' => 'This transaction hash was already submitted']);
        }

        $fr = FundingRequest::create([
            'user_id' => $actor->id,
            'tx_hash' => $tx,
            'amount'  => $amount,
            'status'  => 'pending',
        ]);

        return $fr;
    }
}
