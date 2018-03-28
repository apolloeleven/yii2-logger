<?php
/**
 * Created by PhpStorm.
 * User: zura
 * Date: 3/28/18
 * Time: 11:28 AM
 */

namespace apollo11\logger;


use Yii;
use yii\base\InvalidConfigException;

class EmailTarget extends Target
{
    /**
     * 'components' => [
     *     'log' => [
     *          'targets' => [
     *              [
     *                  'class' => apollo11\logger\EmailTarget::class,
     *                  'levels' => ['error'],
     *                  'excludeKeys' => [],
     *                  'message' => [
     *                      'to' => 'example@example.com',
     *                      'from' => 'example@example.com',
     *                      'subject' => 'Apollo11 Error Logger',
     *                  ]
     *              ],
     *          ],
     *     ],
     * ],
     *
     */
    const CMD_PATH = PHP_BINDIR . '/php ';
    /**
     * @var array
     */
    public $message = [];

    /**
     * @var bool
     */
    public $async = true;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        if (empty($this->message['to'])) {
            throw new InvalidConfigException('The "to" option must be set for EmailTarget::message.');
        }
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        if ($this->async === true) {
            $param=base64_encode(serialize($this));
            $cmd = self::CMD_PATH . Yii::$app->basePath . "/yii async/handle $param > /dev/null 2>/dev/null &";
            exec($cmd);
        } else {
            $this->sendMessage();
        }
    }

    public function sendMessage()
    {
        $subject = !empty($this->message['subject']) ? $this->message['subject'] : 'Error Log';
        $from = !empty($this->message['from']) ? $this->message['from'] : 'test@test.test';
        $to = !empty($this->message['to']) ? $this->message['to'] : 'test@test.test';

        Yii::$app->mailer->compose()
            ->setFrom($from)
            ->setTextBody($this->getFormatMessage())
            ->setTo($to)
            ->setSubject($subject)
            ->send();
    }
}