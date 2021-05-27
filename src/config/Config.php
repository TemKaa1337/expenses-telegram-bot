<?php
declare(strict_types = 1);

namespace App\Config;

class Config
{
    private string $botKey;
    private string $botUsername;
    private string $botWebhookUrl;

    public function __construct()
    {
        $botInfo = json_decode(file_get_contents('config.json'), true);

        $this->botKey = $botInfo['key'];
        $this->botUsername = $botInfo['username'];
        $this->botWebhookUrl = $botInfo['webhook'];
    }

    public function getKey() : string
    {
        return $this->botKey;
    }

    public function getUsername() : string
    {
        return $this->botUsername;
    }

    public function getWebhookUrl() : string
    {
        return $this->botWebhookUrl;
    }
}

?>