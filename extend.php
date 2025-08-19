<?php

use Flarum\Extend;
use Funding\Wallet\Api\Controller\CreateFundingRequestController;
use Funding\Wallet\Api\Controller\ListFundingRequestsController;
use Funding\Wallet\Api\Controller\ApproveFundingRequestController;
use Funding\Wallet\Api\Controller\RejectFundingRequestController;
use Funding\Wallet\Model\FundingRequest;
use Funding\Wallet\Policy\FundingRequestPolicy;
use Funding\Wallet\Listener\SendFundingRequestNotifications;
use Funding\Wallet\Notification\FundingRequestApprovedBlueprint;
use Funding\Wallet\Notification\FundingRequestRejectedBlueprint;

return [
    (new Extend\Frontend('forum'))->js(__DIR__ . '/dist/forum.extension.js'),
    (new Extend\Frontend('admin'))->js(__DIR__ . '/dist/admin.extension.js'),
    new Extend\Locales(__DIR__ . '/locale'),
    (new Extend\Settings())
        ->serializeToForum('fundingWalletDeposit', 'funding-wallet.deposit_address')
        ->serializeToForum('fundingWalletRate', 'funding-wallet.conversion_rate')
        ->serializeToAdmin('fundingWalletDeposit', 'funding-wallet.deposit_address')
        ->serializeToAdmin('fundingWalletRate', 'funding-wallet.conversion_rate'),

    (new Extend\Routes('api'))
        ->post('/funding-requests', 'funding-requests.create', CreateFundingRequestController::class)
        ->get('/funding-requests', 'funding-requests.list', ListFundingRequestsController::class)
        ->post('/funding-requests/{id}/approve', 'funding-requests.approve', ApproveFundingRequestController::class)
        ->post('/funding-requests/{id}/reject', 'funding-requests.reject', RejectFundingRequestController::class),

    (new Extend\Policy())->modelPolicy(FundingRequest::class, FundingRequestPolicy::class),

    (new Extend\Notification())
        ->type(FundingRequestApprovedBlueprint::class, \Flarum\Api\Serializer\BasicUserSerializer::class, ['alert'])
        ->type(FundingRequestRejectedBlueprint::class, \Flarum\Api\Serializer\BasicUserSerializer::class, ['alert']),

    (new Extend\Listeners())->subscribe(SendFundingRequestNotifications::class),
];
