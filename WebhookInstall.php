<?php
declare(strict_types = 1);

namespace App;

use App\Config\BotConfig;

class WebhookInstall
{
    private string $key;
    private string $username;
    private string $webhookUrl;

    public function __construct()
    {
        $botConfig = new BotConfig();

        $this->key = $botConfig->getBotKey();
        $this->username = $botConfig->getBotUsername();
        $this->webhookUrl = $botConfig->getBotWebhookUrl();
    }

    public function setHook()
    {
        
    }
}

(new WebhookInstall)->setHook();