# Funding Requests (Flarum Extension)

Users submit funding requests (tx hash + amount). Admins approve/reject. On approval, the user's **Money** balance is credited using the configured conversion rate.

## Install
```bash
composer require funding/flarum-ext-funding-requests
php flarum migrate
php flarum cache:clear
```

Enable in **Admin → Extensions → Funding Requests** and set:
- **Deposit Address (ERC20)**
- **Conversion Rate** (credits per token)

## Usage
- Forum header shows a **Funding** button.
- Click **Request Funding** → enter TX hash (0x...) and token amount.
- Admins open **Funding** modal to approve or reject. On approval, credits are added to the user's `money` field.

## API
- `POST /api/funding-requests` `{ tx_hash, amount }`
- `GET /api/funding-requests` (admins get all; users get own)
- `POST /api/funding-requests/{id}/approve`
- `POST /api/funding-requests/{id}/reject`

## Requirements
- Flarum ^1.8
- `antoinefr/flarum-ext-money`
