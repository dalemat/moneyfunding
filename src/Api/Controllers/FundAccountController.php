<?php

namespace CryptoFund\ERC20Money\Api\Controllers;

use CryptoFund\ERC20Money\Models\ERC20Transaction;
use CryptoFund\ERC20Money\Services\EthereumService;
use Flarum\Api\Controller\AbstractCreateController;
use Flarum\Http\RequestUtil;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class FundAccountController extends AbstractCreateController
{
    public $serializer = '\CryptoFund\ERC20Money\Api\Serializers\ERC20TransactionSerializer';

    protected $ethereum;

    public function __construct(EthereumService $ethereum)
    {
        $this->ethereum = $ethereum;
    }

    protected function data(ServerRequestInterface $request, Document $document)
    {
        $actor = RequestUtil::getActor($request);
        $actor->assertRegistered();

        $attributes = Arr::get($request->getParsedBody(), 'data.attributes', []);
        
        $txHash = trim(Arr::get($attributes, 'txHash', ''));
        $amount = floatval(Arr::get($attributes, 'amount', 0));

        if (empty($txHash) || $amount <= 0) {
            throw new \InvalidArgumentException('Invalid transaction hash or amount');
        }

        // Check for duplicate transaction
        $existing = ERC20Transaction::where('tx_hash', $txHash)->first();
        if ($existing) {
            throw new \InvalidArgumentException('Transaction already submitted');
        }

        // Get settings
        $settings = app('flarum.settings');
        $minDeposit = floatval($settings->get('cryptofund-erc20-money.min_deposit', 10));
        
        if ($amount < $minDeposit) {
            throw new \InvalidArgumentException("Minimum deposit is {$minDeposit} tokens");
        }

        // Calculate points
        $conversionRate = floatval($settings->get('cryptofund-erc20-money.conversion_rate', 100));
        $points = $amount * $conversionRate;

        // Create pending transaction
        $transaction = ERC20Transaction::create([
            'user_id' => $actor->id,
            'tx_hash' => $txHash,
            'amount' => $amount,
            'points' => $points,
            'status' => 'pending',
            'contract_address' => $settings->get('cryptofund-erc20-money.contract_address'),
            'block_number' => null,
            'confirmation_count' => 0
        ]);

        // Try to verify immediately
        try {
            $this->ethereum->verifyTransaction($transaction);
        } catch (\Exception $e) {
            // Verification will happen via cron job
        }

        return $transaction;
    }
}
