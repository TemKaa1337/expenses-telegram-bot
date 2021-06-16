<?php
declare(strict_types = 1);

namespace App;

include('vendor/autoload.php');

use App\Config\BotConfig;

class WebhookInstall
{
    private string $key;
    private string $username;
    private string $webhookUrl;
    private string $certificatePath;

    public function __construct()
    {
        $botConfig = new BotConfig();

        $this->key = $botConfig->getBotKey();
        $this->username = $botConfig->getBotUsername();
        $this->webhookUrl = $botConfig->getBotWebhookUrl();
        $this->webhookInstallUrl = $botConfig->getInstallWebhookUrl();
        $this->certificatePath = $botConfig->getCertificatePath();
    }

    public function setHook()
    {
        $ch = curl_init();

        $optArray =[
            CURLOPT_URL => $this->webhookInstallUrl,
            CURLOPT_POST => true,
            CURLOPT_SAFE_UPLOAD => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => array('url' => $this->webhookUrl, 'certificate' => '@' . $this->certificatePath)
        ];
        
        curl_setopt_array($ch, $optArray);
        
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }
}

(new WebhookInstall)->setHook();