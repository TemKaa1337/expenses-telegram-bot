<?php
declare(strict_types = 1);

namespace App\Config;

class BotConfig
{
    private string $botKey;
    private string $botUsername;
    private string $botWebhookUrl;
    private string $certificatePath;
    private string $installWebhookUrl;

    public function __construct()
    {
        $botInfo = json_decode(file_get_contents('secret/bot_config.json'), true);

        $this->botKey = $botInfo['key'];
        $this->botUsername = $botInfo['username'];
        $this->botWebhookUrl = $botInfo['webhook'];
        $this->installWebhookUrl = $botInfo['setWebhook'];
        $this->certificatePath = $botInfo['certificatePath'];
    }

    public function getBotKey() : string
    {
        return $this->botKey;
    }

    public function getBotUsername() : string
    {
        return $this->botUsername;
    }

    public function getBotWebhookUrl() : string
    {
        return $this->botWebhookUrl;
    }

    public function getInstallWebhookUrl() : string
    {
        return $this->installWebhookUrl;
    }

    public function getCertificatePath() : string
    {
        return $this->certificatePath;
    }
}

?>