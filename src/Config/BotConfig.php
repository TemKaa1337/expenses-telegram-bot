<?php
declare(strict_types = 1);

namespace App\Config;

use App\Config\Config;

class BotConfig implements Config
{
    private readonly array $config;

    public function __construct()
    {
	    $botInfo = json_decode(file_get_contents(__DIR__.'/Secret/bot_config.json'), true);
        $this->config = [
            'botKey' => $botInfo['key'],
            'botUsername' => $botInfo['username'],
            'botWebhookUrl' => $botInfo['webhook'],
            'installWebhookUrl' => $botInfo['setWebhook'],
            'certificatePath' => $botInfo['certificatePath']
        ];
    }

    public function getConfig(): array
    {
        return $this->config;
    }
}

?>
