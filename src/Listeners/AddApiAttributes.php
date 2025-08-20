<?php

namespace CryptoFund\ERC20Money\Listeners;

use Flarum\Api\Event\Serializing;
use Flarum\Api\Serializer\ForumSerializer;
use Flarum\Settings\SettingsRepositoryInterface;

class AddApiAttributes
{
    protected $settings;
    
    public function __construct(SettingsRepositoryInterface $settings)
    {
        $this->settings = $settings;
    }
    
    public function __invoke(Serializing $event): void
    {
        if ($event->isSerializer(ForumSerializer::class)) {
            $event->attributes['cryptofundERC20Settings'] = [
                'wallet_address' => $this->settings->get('cryptofund-erc20-money.wallet_address'),
                'contract_address' => $this->settings->get('cryptofund-erc20-money.contract_address'),
                'min_deposit' => (float) $this->settings->get('cryptofund-erc20-money.min_deposit', 10),
                'conversion_rate' => (float) $this->settings->get('cryptofund-erc20-money.conversion_rate', 100)
            ];
        }
    }
}
