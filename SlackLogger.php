<?php
/**
 * Created by PhpStorm.
 * User: zura
 * Date: 3/14/18
 * Time: 5:06 PM
 */

namespace apollo11\logger;

use Exception;
use function GuzzleHttp\Psr7\str;
use yii\httpclient\Client;


class SlackLogger extends Target
{

    /**
     * @var string incoming webhook URL.
     */
    public $webhookUrl;

    /**
     * @var string incoming username.
     */
    public $username;

    /**
     * @var string incoming detailsUrl.
     */
    public $detailsUrl;

    /**
     * @var string incoming icon.
     */
    public $icon;

    /**
     * @var string incoming title.
     */
    public $title;

    /**
     * @var array incoming LEVEL.
     */
    const LEVELS = ['', 'Error'];

    /**
     * @var Client|array|string Yii HTTP client configuration.
     * This can be a component ID, a configuration array or a Client instance.
     */
    public $httpClient;

    public function run()
    {
        return 'SlackLogger!';
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
        $response = $this->httpClient
            ->post($this->webhookUrl, $this->loadParams($this->getFormatMessage()))
            ->setFormat(Client::FORMAT_JSON)
            ->send();
        if (!$response->getIsOk()) {
            var_dump($response->getContent());
            throw new Exception(
                'Unable to send logs to Slack: ' . $response->getContent()
            );
        }
    }

    protected function loadParams($text)
    {
        return [
            'username' => $this->username,
            'icon_emoji' => $this->icon,
            'attachments' => [
                [
                    'fallback' => 'Required plain-text summary of the attachment.',
                    'color' => '#e42e0c',
                    'title' => $this->title,
                    'title_link' => 'https://github.com/apolloeleven/yii2-logger',
                    'text' => '```' . PHP_EOL . (string)$text . PHP_EOL . '```',
                    'fields' => [
                        [
                            'title' => 'Level',
                            //'value'=> '*`'.self::LEVELS[$level].'`*',
                            'short' => true,
                        ],
                        [
                            'title' => 'Category',
                            //'value'=> '`'.$category.'`',
                            'short' => true,
                        ]
                    ],

                    'actions' => [
                        [
                            'text' => 'For More Details, Click Here',
                            'url' => $this->detailsUrl,
                            'type' => 'button',
                            'style' => 'primary'
                        ]
                    ],
                    //'ts'=>$timestamp
                ]
            ]
        ];
    }
}