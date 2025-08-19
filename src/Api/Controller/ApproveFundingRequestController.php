<?php

namespace Funding\Wallet\Api\Controller;

use Funding\Wallet\Api\Serializer\FundingRequestSerializer;
use Funding\Wallet\Model\FundingRequest;
use Funding\Wallet\Notification\FundingRequestApprovedBlueprint;
use Flarum\Api\Controller\AbstractShowController;
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Exception\PermissionDeniedException;
use Flarum\Notification\NotificationSyncer;
use Illuminate\Support\Facades\DB;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ApproveFundingRequestController extends AbstractShowController
{
    public $serializer = FundingRequestSerializer::class;

    public function data(ServerRequestInterface $request, Document $document)
    {
        $actor = $request->getAttribute('actor');
        if (!$actor || !$actor->isAdmin()) {
            throw new PermissionDeniedException();
        }

        $id = (int) ($request->getAttribute('id'));
        /** @var FundingRequest $fr */
        $fr = FundingRequest::findOrFail($id);

        if ($fr->status !== 'pending') {
            return $fr;
        }

        /** @var SettingsRepositoryInterface $settings */
        $settings = resolve(SettingsRepositoryInterface::class);
        $rate = (float) ($settings->get('funding-wallet.conversion_rate') ?: '0');

        if ($rate <= 0) {
            throw new \InvalidArgumentException('Invalid conversion rate in settings.');
        }

        $credit = (float) $fr->amount * $rate;

        DB::transaction(function () use ($fr, $credit) {
            // lock user row
            $user = $fr->user()->lockForUpdate()->first();
            $current = (float) ($user->money ?? 0);
            $user->money = $current + $credit;
            $user->save();

            $fr->status = 'approved';
            $fr->save();
        });

        // notify user
        /** @var NotificationSyncer $syncer */
        $syncer = resolve(NotificationSyncer::class);
        $syncer->sync(new FundingRequestApprovedBlueprint($fr), [$fr->user]);

        return $fr;
    }
}
