<?php

namespace CryptoFund\ERC20Money\Notifications;

use Flarum\Notification\Blueprint\BlueprintInterface;
use Flarum\Notification\MailableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PointsCreditedBlueprint implements BlueprintInterface, MailableInterface
{
    public $amount;

    public function __construct($amount)
    {
        $this->amount = $amount;
    }

    public function getFromUser()
    {
        return null;
    }

    public function getSubject()
    {
        return null;
    }

    public function getData()
    {
        return ['amount' => $this->amount];
    }

    public static function getType()
    {
        return 'erc20PointsCredited';
    }

    public static function getSubjectModel()
    {
        return null;
    }

    public function getEmailView()
    {
        return ['text' => 'cryptofund-erc20-money::emails.points-credited'];
    }

    public function getEmailSubject(TranslatorInterface $translator)
    {
        return $translator->trans('cryptofund-erc20-money.email.points_credited_subject');
    }
}
