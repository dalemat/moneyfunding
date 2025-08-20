<?php

namespace CryptoFund\ERC20Money\Services;

use CryptoFund\ERC20Money\Models\ERC20Transaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class EthereumService
{
    protected $client;
    protected $settings;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
        $this->settings = app('flarum.settings');
    }

    public function verifyTransaction(ERC20Transaction $transaction)
    {
        $rpcUrl = $this->settings->get('cryptofund-erc20-money.ethereum_rpc_url');
        $walletAddress = strtolower($this->settings->get('cryptofund-erc20-money.wallet_address'));
        $contractAddress = strtolower($this->settings->get('cryptofund-erc20-money.contract_address'));

        // Get transaction receipt
        $response = $this->client->post($rpcUrl, [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'eth_getTransactionReceipt',
                'params' => [$transaction->tx_hash],
                'id' => 1
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        
        if (!isset($data['result']) || !$data['result']) {
            throw new \Exception('Transaction not found or pending');
        }

        $receipt = $data['result'];
        
        // Check if transaction was successful
        if ($receipt['status'] !== '0x1') {
            $transaction->update(['status' => 'failed']);
            throw new \Exception('Transaction failed');
        }

        // For ERC20 transfers, check the logs
        $transferFound = false;
        $transferAmount = 0;

        foreach ($receipt['logs'] as $log) {
            // ERC20 Transfer event signature: Transfer(address,address,uint256)
            $transferTopic = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';
            
            if (strtolower($log['address']) === $contractAddress && 
                isset($log['topics'][0]) && 
                $log['topics'][0] === $transferTopic) {
                
                // Check if transfer is to our wallet
                $toAddress = '0x' . substr($log['topics'][2], 26); // Remove padding
                
                if (strtolower($toAddress) === $walletAddress) {
                    $transferFound = true;
                    $transferAmount = hexdec($log['data']) / pow(10, 18); // Assuming 18 decimals
                    break;
                }
            }
        }

        if (!$transferFound) {
            $transaction->update(['status' => 'failed']);
            throw new \Exception('Transfer to wallet not found');
        }

        // Verify amount matches (with small tolerance for gas differences)
        $expectedAmount = $transaction->amount;
        if (abs($transferAmount - $expectedAmount) > ($expectedAmount * 0.01)) {
            $transaction->update(['status' => 'failed']);
            throw new \Exception('Transfer amount mismatch');
        }

        // Get current block number for confirmations
        $currentBlockResponse = $this->client->post($rpcUrl, [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'eth_blockNumber',
                'params' => [],
                'id' => 2
            ]
        ]);

        $currentBlockData = json_decode($currentBlockResponse->getBody(), true);
        $currentBlock = hexdec($currentBlockData['result']);
        $txBlock = hexdec($receipt['blockNumber']);
        $confirmations = $currentBlock - $txBlock + 1;

        // Update transaction
        $transaction->update([
            'block_number' => $txBlock,
            'confirmation_count' => $confirmations,
            'status' => $confirmations >= 12 ? 'confirmed' : 'pending'
        ]);

        // Credit user balance if confirmed
        if ($confirmations >= 12) {
            $this->creditUserBalance($transaction);
        }

        return $transaction;
    }

    protected function creditUserBalance(ERC20Transaction $transaction)
    {
        if ($transaction->status !== 'confirmed') {
            return;
        }

        $user = $transaction->user;
        $currentBalance = $user->erc20_balance ?? 0;
        
        $user->erc20_balance = $currentBalance + $transaction->points;
        $user->save();

        Log::info("Credited {$transaction->points} points to user {$user->id}");
    }
}
