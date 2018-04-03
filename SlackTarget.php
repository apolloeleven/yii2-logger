<?php
/**
 * Created by PhpStorm.
 * User: zura
 * Date: 3/14/18
 * Time: 5:06 PM
 */

namespace apollo11\logger;

use Exception;
use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\httpclient\Client;
use yii\log\Logger;


class SlackTarget extends Target
{
    /**
     * @var string incoming webhook URL.
     */
    public $webhookUrl;

    /**
     * @var string incoming webhook URL.
     */
    public $title_link;

    /**
     * @var string incoming webhook URL.
     */
    public $icon_url;

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
    public $icon_emoji;

    /**
     * @var string incoming title.
     */
    public $title;

    /**
     * @var boolean Whether to mention channel members or not
     */
    public $mentionChannelMembers = false;

    private $level = 99;

    private $categorty = [];


    /**
     * @var Client|array|string Yii HTTP client configuration.
     * This can be a component ID, a configuration array or a Client instance.
     */
    public $httpClient;

    public $messageColors = [
        Logger::LEVEL_ERROR => 'danger',
        Logger::LEVEL_WARNING => 'warning',
        Logger::LEVEL_INFO => '#aee1ff',
        Logger::LEVEL_TRACE => 'good'
    ];

    /**
     * @inheritDoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->httpClient = new Client();
    }

    protected function prepareConfig()
    {
        /** @var $exception ErrorException */

        $title = [];

        foreach ($this->messages as $message) {
            list($exception, $level, $category, $timestamp) = $message;
            $this->level = $this->level < $level ? $this->level : $level;
            array_push($this->categorty, $category);
            if (is_string($exception)) {
                $title[] = strlen($exception) < 50 ? $exception : '';
            } else {
                $title[] = strlen($exception->getMessage()) < 50 ? $exception->getMessage() : '';
            }
        }


        $this->config = [
            'username' => $this->username,
            'icon_url' => $this->icon_url,
            'icon_emoji' => $this->icon_emoji,
            'attachments' => [
                [
                    'fallback' => 'Required plain-text summary of the attachment.',
                    'color' => $this->messageColors[$this->level],
                    'title' => $this->title ?: rtrim(implode(',', array_unique($title)), ","),
                    'title_link' => $this->title_link,
                    'text' => ($this->mentionChannelMembers ? '<!channel>' : '') . '```' . PHP_EOL . $this->getFormatMessage() . PHP_EOL . '```',
                    'fields' => [
                        [
                            'title' => 'Level',
                            'value' => '`' . Logger::getLevelName($this->level) . '`',
                            'short' => true,
                        ],
                        [
                            'title' => 'Category',
                            'value' => '`' . implode(',', array_unique($this->categorty)) . '`',
                            'short' => true,
                        ]
                    ],
                    'ts' => $this->messages[0][3]
                ]
            ]
        ];

        if ($this->detailsUrl) {
            $this->config['attachments'][0]['actions'] = [
                [
                    'text' => 'For More Details, Click Here',
                    'url' => $this->detailsUrl,
                    'type' => 'button',
                    'style' => 'primary'
                ]
            ];
        }

    }

    public function sendMessage()
    {
        $response = $this->httpClient
            ->post($this->webhookUrl, $this->config)
            ->setFormat(Client::FORMAT_JSON)
            ->send();
        if (!$response->getIsOk()) {
//            var_dump($response->getContent());
            throw new Exception(
                'Unable to send logs to Slack: ' . $response->getContent()
            );
        }
    }
}