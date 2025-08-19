<?php

namespace Funding\Wallet\Api\Controller;

use Funding\Wallet\Api\Serializer\FundingRequestSerializer;
use Funding\Wallet\Model\FundingRequest;
use Funding\Wallet\Notification\FundingRequestRejectedBlueprint;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\Notification\NotificationSyncer;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class RejectFundingRequestController extends AbstractShowController
{
    public $serializer = FundingRequestSerializer::class;

    public function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        if (!$actor || !$actor->isAdmin()) {
            throw new PermissionDeniedException();
        }

        $id = (int) ($request->getAttribute('id'));
        $body = (array) $request->getParsedBody();
        $reason = trim((string) ($body['reason'] ?? ''));

        /** @var FundingRequest $fr */
        $fr = FundingRequest::findOrFail($id);

        if ($fr->status !== 'pending') {
            return $fr;
        }

        $fr->status = 'rejected';
        $fr->reason = $reason ?: null;
        $fr->save();

        /** @var NotificationSyncer $syncer */
        $syncer = resolve(NotificationSyncer::class);
        $syncer->sync(new FundingRequestRejectedBlueprint($fr), [$fr->user]);

        return $fr;
    }
}
