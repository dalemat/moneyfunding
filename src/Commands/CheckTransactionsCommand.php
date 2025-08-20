<?php

namespace CryptoFund\ERC20Money\Commands;

use CryptoFund\ERC20Money\Models\ERC20Transaction;
use CryptoFund\ERC20Money\Services\EthereumService;
use Illuminate\Console\Command;

class CheckTransactionsCommand extends Command
{
    protected $signature = 'erc20:check-transactions';
    protected $description = 'Check pending ERC20 transactions for confirmations';

    protected $ethereum;

    public function __construct(EthereumService $ethereum)
    {
        parent::__construct();
        $this->ethereum = $ethereum;
    }

    public function handle()
    {
        $this->info('Checking pending ERC20 transactions...');

        $pendingTransactions = ERC20Transaction::whereIn('status', ['pending', 'confirming'])
            ->orderBy('created_at', 'asc')
            ->limit(100)
            ->get();

        $confirmed = 0;
        $failed = 0;

        foreach ($pendingTransactions as $transaction) {
            try {
                $this->ethereum->verifyTransaction($transaction);
                
                if ($transaction->fresh()->status === 'confirmed') {
                    $confirmed++;
                    $this->info("✅ Transaction {$transaction->tx_hash} confirmed");
                }
            } catch (\Exception $e) {
                $this->error("❌ Transaction {$transaction->tx_hash} failed: " . $e->getMessage());
                $transaction->update(['status' => 'failed']);
                $failed++;
            }
        }

        $this->info("Processed {$confirmed} confirmed transactions, {$failed} failed");
        
        return 0;
    }
}
