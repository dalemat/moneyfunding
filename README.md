# CryptoFund ERC20 Money Extension

A Flarum extension that allows users to fund their accounts using ERC20 tokens on Ethereum blockchain.

## Features

- Accept ERC20 token deposits
- Automatic transaction verification
- Real-time balance updates
- Admin dashboard for monitoring
- Email notifications for successful transactions

## Installation

1. Copy this extension to `extensions/cryptofund-erc20-money/`
2. Run `composer install --no-dev` in the extension directory
3. Enable the extension in Flarum admin panel
4. Configure Ethereum settings in admin panel
5. Set up cron job for transaction checking

## Configuration

Configure these settings in admin panel:
- **Ethereum RPC URL**: Your Ethereum node URL
- **Contract Address**: ERC20 token contract address
- **Wallet Address**: Your receiving wallet address
- **Conversion Rate**: Points per token
- **Minimum Deposit**: Minimum token amount

## Usage

Users can fund their accounts by:
1. Going to their profile page
2. Clicking "Fund Account" button
3. Sending tokens to your wallet address
4. Submitting their transaction hash
5. Waiting for automatic verification

## License

MIT License
