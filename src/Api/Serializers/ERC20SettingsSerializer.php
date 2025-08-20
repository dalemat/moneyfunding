<?php

namespace CryptoFund\ERC20Money\Api\Serializers;

use Flarum\Api\Serializer\AbstractSerializer;

class ERC20SettingsSerializer extends AbstractSerializer
{
    protected $type = 'erc20-settings';
    
    protected function getDefaultAttributes($settings)
    {
        return [
            'ethereum_rpc_url' => $settings['ethereum_rpc_url'] ?? '',
            'contract_address' => $settings['contract_address'] ?? '',
            'wallet_address' => $settings['wallet_address'] ?? '',
            'conversion_rate' => $settings['conversion_rate'] ?? 100,
            'min_deposit' => $settings['min_deposit'] ?? 10
        ];
    }
}
