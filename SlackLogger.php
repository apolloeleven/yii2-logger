<?php
/**
 * Created by PhpStorm.
 * User: zura
 * Date: 3/14/18
 * Time: 5:06 PM
 */

namespace apollo11\logger;
use Exception;
use yii\httpclient\Client;
use yii\log\Target;


class SlackLogger extends  Target
{

    /**
     * @var string incoming webhook URL.
     */
    public $webhookUrl;

    /**
     * @var Client|array|string Yii HTTP client configuration.
     * This can be a component ID, a configuration array or a Client instance.
     */
    public $httpClient;

    public function run()
    {
        return "SlackLogger!";
    }
    /**
     * @inheritDoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->httpClient = new Client();
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        $error = $this->filterMessages($this->messages,1);
        foreach ($error as $message) {
            list($text, $level, $category, $timestamp) = $message;
            if ($level === 1) {
                $response = $this->httpClient
                    ->post($this->webhookUrl, $this->loadParams($text))
                    ->setFormat(Client::FORMAT_JSON)
                    ->send();
                if (!$response->getIsOk()) {
                    throw new Exception(
                        'Unable to send logs to Slack: ' . $response->getContent()
                    );
                }
            }
        }
    }

    protected function loadParams($text){
        return ['text'=>(string)$text];
    }
}