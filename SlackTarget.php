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
    const CMD_PATH = PHP_BINDIR . '/php ';
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
     * @var string incoming async.
     */
    public $async = true;

    /**
     * @var boolean Whether to mention channel members or not
     */
    public $mentionChannelMembers = false;

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

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        if ($this->async === true) {
            $param = base64_encode(serialize($this));
            $cmd = self::CMD_PATH . Yii::$app->basePath . "/yii async/handle $param > /dev/null 2>/dev/null &";
            exec($cmd);
        } else {
            $this->sendMessage();
        }
    }

    protected function loadParams($message)
    {
        /** @var $exception ErrorException */
        list($exception, $level, $category, $timestamp) = $this->messages[0];
        $slackConfig = [
            'username' => $this->username,
            'icon_url' => $this->icon_url,
            'icon_emoji' => $this->icon_emoji,
            'attachments' => [
                [
                    'fallback' => 'Required plain-text summary of the attachment.',
                    'color' => $this->messageColors[$level],
                    'title' => $this->title ?: $exception->getMessage(),
                    'title_link' => $this->title_link,
                    'text' => ($this->mentionChannelMembers ? '<!channel>' : '') . '```' . PHP_EOL . $message . PHP_EOL . '```',
                    'fields' => [
                        [
                            'title' => 'Level',
                            'value' => '`' . Logger::getLevelName($level) . '`',
                            'short' => true,
                        ],
                        [
                            'title' => 'Category',
                            'value' => '`' . $category . '`',
                            'short' => true,
                        ]
                    ],
                    'ts' => $timestamp
                ]
            ]
        ];

        if ($this->detailsUrl) {
            $slackConfig['attachments'][0]['actions'] = [
                [
                    'text' => 'For More Details, Click Here',
                    'url' => $this->detailsUrl,
                    'type' => 'button',
                    'style' => 'primary'
                ]
            ];
        }

        return $slackConfig;
    }

    public function sendMessage()
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
}