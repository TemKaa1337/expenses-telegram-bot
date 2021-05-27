<?php
declare(strict_types = 1);

namespace App;

require('vendor/autoload.php');

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;

class WebhookInstall
{
    private string $key;
    private string $username;
    private string $webhookUrl;

    public function __construct()
    {
        $botInfo = json_decode(file_get_contents('config.json'), true);

        $this->key = $botInfo['key'];
        $this->username = $botInfo['username'];
        $this->webhookUrl = $botInfo['webhook'];
    }

    public function setHook()
    {
        try {
            $telegram = new Telegram($this->key, $this->username);

            $webhook = $telegram->setWebhook($this->webhookUrl, ['certificate' => '/path/to/certificate']);
            
            if ($webhook->isOk()) {
                echo $webhook->getDescription();
            } else var_dump($webhook);

        } catch (TelegramException $e) {
            echo $e->getMessage();
        }
    }
}

(new WebhookInstall)->setHook();