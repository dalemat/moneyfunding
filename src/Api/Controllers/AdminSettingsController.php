<?php

namespace CryptoFund\ERC20Money\Api\Controllers;

use Flarum\Api\Controller\AbstractShowController;
use Flarum\Http\RequestUtil;
use Flarum\Settings\SettingsRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class AdminSettingsController extends AbstractShowController
{
    public $serializer = 'CryptoFund\ERC20Money\Api\Serializers\ERC20SettingsSerializer';
    
    protected $settings;
    
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }
    
    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertAdmin();
        
        return [
            'ethereum_rpc_url' => $this->settings->get('cryptofund-erc20-money.ethereum_rpc_url'),
            'contract_address' => $this->settings->get('cryptofund-erc20-money.contract_address'),
            'wallet_address' => $this->settings->get('cryptofund-erc20-money.wallet_address'),
            'conversion_rate' => $this->settings->get('cryptofund-erc20-money.conversion_rate', 100),
            'min_deposit' => $this->settings->get('cryptofund-erc20-money.min_deposit', 10)
        ];
    }
}
