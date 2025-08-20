# Funding Wallet — Funding Requests

Admin-approved ERC20 funding requests for Flarum, integrated with the Money extension.
Users submit a TX hash + amount; admins approve to credit Money balance via a conversion rate.

## Install
```bash
composer require funding/flarum-ext-funding-requests
php flarum migrate
php flarum cache:clear
```

Enable the extension in Admin, then set:
- **Deposit Address (ERC20)** — your central wallet address
- **Conversion Rate** — forum credits per 1 token

## API
- `POST /funding-requests` — create request `{ tx_hash, amount }`
- `GET /funding-requests` — list (admin sees all, user sees own)
- `POST /funding-requests/{id}/approve`
- `POST /funding-requests/{id}/reject`

## Notes
- Requires `antoinefr/flarum-ext-money` installed/enabled.
- Approval credits the user's `money` field with `amount * conversion_rate`.
