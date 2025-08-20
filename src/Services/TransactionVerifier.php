<?php

namespace CryptoFund\ERC20Money\Services;

use CryptoFund\ERC20Money\Models\ERC20Transaction;
use Flarum\Settings\SettingsRepositoryInterface;

class TransactionVerifier
{
    protected $ethereumService;
    protected $pointsManager;
    protected $notificationService;
    protected $settings;
    
    public function __construct(
        EthereumService $ethereumService,
        PointsManager $pointsManager,
        NotificationService $notificationService,
        SettingsRepositoryInterface $settings
    ) {
        $this->ethereumService = $ethereumService;
        $this->pointsManager = $pointsManager;
        $this->notificationService = $notificationService;
        $this->settings = $settings;
    }
    
    public function verify(ERC20Transaction $transaction): bool
    {
        try {
            // Get transaction from blockchain
            $tx = $this->ethereumService->getTransaction($transaction->tx_hash);
            if (!$tx) {
                return false; // Transaction not found or not yet mined
            }
            
            // Get transaction receipt
            $receipt = $this->ethereumService->getTransactionReceipt($transaction->tx_hash);
            if (!$receipt) {
                return false; // Receipt not available
            }
            
            // Check if transaction failed
            if ($receipt['status'] === '0x0') {
                $transaction->markAsFailed();
                $this->notificationService->notifyTransactionFailed($transaction);
                return false;
            }
            
            // Verify transaction details
            $expectedWallet = strtolower($this->settings->get('cryptofund-erc20-money.wallet_address'));
            $contractAddress = strtolower($this->settings->get('cryptofund-erc20-money.contract_address'));
            
            if (!$expectedWallet || !$contractAddress) {
                throw new \Exception('Wallet or contract address not configured');
            }
            
            // For ERC20 transfers, check the contract address and decode transfer data
            if (strtolower($tx['to']) !== $contractAddress) {
                $transaction->markAsFailed();
                return false;
            }
            
            $transferData = $this->ethereumService->decodeERC20Transfer($tx['input']);
            if (!$transferData) {
                $transaction->markAsFailed();
                return false;
            }
            
            // Verify recipient address
            if (strtolower($transferData['to']) !== $expectedWallet) {
                $transaction->markAsFailed();
                return false;
            }
            
            // Verify amount (convert from smallest unit)
            $actualAmount = $this->ethereumService->weiToEther($transferData['amount']);
            $expectedAmount = (string) $transaction->amount;
            
            if (bccomp($actualAmount, $expectedAmount, 6) !== 0) {
                $transaction->markAsFailed();
                return false;
            }
            
            // Transaction is valid - mark as confirmed and credit points
            $blockNumber = hexdec($receipt['blockNumber']);
            $transaction->markAsConfirmed($blockNumber);
            $transaction->from_address = $tx['from'];
            $transaction->to_address = $transferData['to'];
            $transaction->save();
            
            // Credit points to user
            $this->pointsManager->creditPoints($transaction->user, $transaction->points);
            
            // Send notification
            $this->notificationService->notifyPointsCredited($transaction);
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Transaction verification failed: ' . $e->getMessage(), [
                'tx_hash' => $transaction->tx_hash,
                'transaction_id' => $transaction->id
            ]);
            return false;
        }
    }
    
    public function verifyAsync(ERC20Transaction $transaction): void
    {
        // In a real implementation, you'd queue this job
        // For now, we'll just call verify directly
        $this->verify($transaction);
    }
}
