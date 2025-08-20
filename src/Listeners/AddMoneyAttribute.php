<?php

namespace Cryptoforex\Money\Listeners;

use Flarum\Api\Serializer\UserSerializer;
use Flarum\User\User;

class AddMoneyAttribute
{
    public function __invoke(UserSerializer $serializer, User $user, array $attributes): array
    {
        $attributes['money'] = $user->money ?? 0;
        return $attributes;
    }
}
