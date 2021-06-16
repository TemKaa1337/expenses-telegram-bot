<?php
declare(strict_types = 1);

namespace App;

require('vendor/autoload.php');

use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\Exception\TelegramException;

class WebhookUninstall
{
    private string $key;
    private string $username;

    public function __construct()
    {
        $botInfo = json_decode(file_get_contents('config.json'), true);

        $this->key = $botInfo['key'];
        $this->username = $botInfo['username'];
    }

    public function unsetHook()
    {
        try {
            $telegram = new Telegram($this->key, $this->username);

            $result = $telegram->deleteWebhook();
        
            echo $result->getDescription();
        } catch (TelegramException $e) {
            echo $e->getMessage();
        }
    }
}

(new WebhookUninstall)->unsetHook();