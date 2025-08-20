<?php

use Flarum\Extend;
use Cryptoforex\Money\Listeners\AddMoneyAttribute;

return [
    (new Extend\ApiSerializer(\Flarum\Api\Serializer\UserSerializer::class))
        ->attributes(AddMoneyAttribute::class),
];
