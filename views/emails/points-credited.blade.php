Hello {{ $user->display_name ?? $user->username }},

{{ $amount }} points have been successfully added to your account from your ERC20 token deposit.

Your new balance is {{ $user->erc20_balance }} points.

You can view your transaction history at: {{ $url->to('forum')->route('erc20.transactions') }}

Best regards,
{{ $settings->get('forum_title') }} Team
