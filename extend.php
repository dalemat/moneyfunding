<?php

use Flarum\Extend;

return [
    (new Extend\Frontend('forum'))->js(__DIR__.'/dist/forum.js'),
    (new Extend\Frontend('admin'))->js(__DIR__.'/dist/admin.js'),
    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->post('/funding-requests', 'funding-requests.create', \Funding\Wallet\Api\Controller\CreateFundingRequestController::class)
        ->get('/funding-requests', 'funding-requests.list', \Funding\Wallet\Api\Controller\ListFundingRequestsController::class)
        ->post('/funding-requests/{id}/approve', 'funding-requests.approve', \Funding\Wallet\Api\Controller\ApproveFundingRequestController::class)
        ->post('/funding-requests/{id}/reject', 'funding-requests.reject', \Funding\Wallet\Api\Controller\RejectFundingRequestController::class),

    (new Extend\Model(\Funding\Wallet\Model\FundingRequest::class)),
];
