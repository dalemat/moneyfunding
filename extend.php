<?php

use Flarum\Extend;
use Funding\Requests\Api\Controller\CreateFundingRequestController;
use Funding\Requests\Api\Controller\ListFundingRequestsController;
use Funding\Requests\Api\Controller\ApproveFundingRequestController;
use Funding\Requests\Api\Controller\RejectFundingRequestController;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/dist/forum.js')
        ->css(__DIR__.'/dist/forum.css'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/dist/admin.js'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->post('/funding-requests', 'funding-requests.create', CreateFundingRequestController::class)
        ->get('/funding-requests', 'funding-requests.list', ListFundingRequestsController::class)
        ->post('/funding-requests/{id}/approve', 'funding-requests.approve', ApproveFundingRequestController::class)
        ->post('/funding-requests/{id}/reject', 'funding-requests.reject', RejectFundingRequestController::class),
];