<?php

namespace CryptoFund\ERC20Money\Providers;

use CryptoFund\ERC20Money\Commands\CheckTransactionsCommand;
use Flarum\Foundation\AbstractServiceProvider;

class ERC20ServiceProvider extends AbstractServiceProvider
{
    public function register(): void
    {
        $this->container->bind(CheckTransactionsCommand::class, function ($container) {
            return $container->make(CheckTransactionsCommand::class);
        });
    }
    
    public function boot(): void
    {
        if ($this->container->bound('flarum.console.commands')) {
            $this->container->extend('flarum.console.commands', function ($commands) {
                $commands[] = CheckTransactionsCommand::class;
                return $commands;
            });
        }
    }
}
