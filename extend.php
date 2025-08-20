<?php

use Flarum\Extend;
use CryptoFund\ERC20Money\Api\Controllers\FundAccountController;
use CryptoFund\ERC20Money\Api\Controllers\ListTransactionsController;
use CryptoFund\ERC20Money\Api\Serializers\ERC20TransactionSerializer;
use CryptoFund\ERC20Money\Commands\CheckTransactionsCommand;
use CryptoFund\ERC20Money\Listeners\CreditPointsListener;

return [
    (new Extend\Frontend('forum'))
        ->js(__DIR__.'/js/dist/forum.js')
        ->css(__DIR__.'/less/forum.less'),

    (new Extend\Frontend('admin'))
        ->js(__DIR__.'/js/dist/admin.js')
        ->css(__DIR__.'/less/admin.less'),

    new Extend\Locales(__DIR__.'/locale'),

    (new Extend\Routes('api'))
        ->post('/erc20/fund', 'erc20.fund', FundAccountController::class)
        ->get('/erc20/transactions', 'erc20.transactions', ListTransactionsController::class),

    (new Extend\Database)
        ->migration(__DIR__.'/migrations/01_create_erc20_transactions_table.php')
        ->migration(__DIR__.'/migrations/02_add_erc20_balance_to_users.php'),

    (new Extend\Console())
        ->command(CheckTransactionsCommand::class),

    (new Extend\User())
        ->registerPreference('erc20_balance', 'floatval', 0),

    (new Extend\Event())
        ->listen(\Flarum\User\Event\Saving::class, CreditPointsListener::class),

    (new Extend\Settings())
        ->default('cryptofund-erc20-money.ethereum_rpc_url', 'https://mainnet.infura.io/v3/')
        ->default('cryptofund-erc20-money.conversion_rate', '100')
        ->default('cryptofund-erc20-money.min_deposit', '10')
        ->serializeToForum('cryptofundERC20Settings', [
            'cryptofund-erc20-money.contract_address',
            'cryptofund-erc20-money.wallet_address',
            'cryptofund-erc20-money.conversion_rate',
            'cryptofund-erc20-money.min_deposit'
        ]),

    (new Extend\ApiSerializer(\Flarum\Api\Serializer\UserSerializer::class))
        ->attribute('erc20Balance', function ($serializer, $user) {
            return (float) $user->erc20_balance;
        }),
];
