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
        curl_setopt($ch, CURLOPT_URL, $this->webhookInstallUrl);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_BUFFERSIZE, 128);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array('url' => $this->webhookUrl, 'certificate' => new \CURLFile($this->certificatePath)));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec ($ch);
        var_dump($response);
        curl_close($ch);
    }
}

(new WebhookInstall)->setHook();
