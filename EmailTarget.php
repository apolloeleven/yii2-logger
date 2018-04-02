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
use yii\di\Instance;
use yii\log\LogRuntimeException;
use yii\mail\MailerInterface;

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
class EmailTarget extends Target
{
    /**
     * @var array
     */
    public $message = [];

    /**
     * @var MailerInterface|array|string the mailer object or the application component ID of the mailer object.
     * After the EmailTarget object is created, if you want to change this property, you should only assign it
     * with a mailer object.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $mailer = 'mailer';

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (empty($this->message['to'])) {
            throw new InvalidConfigException('The "to" option must be set for EmailTarget::message.');
        }
        $this->mailer = Instance::ensure($this->mailer, 'yii\mail\MailerInterface');
    }

    /**
     *
     *
     * @author Zura Sekhniashvili <zurasekhniashvili@gmail.com>
     * @throws InvalidConfigException
     * @throws LogRuntimeException
     */
    public function sendMessage()
    {
        if (!isset($this->config['formattedMessage'])){
            throw new InvalidConfigException('`formattedMessage` was not found in $config object. Maybe you forgot to call ->prepareConfig() method?');
        }
        // https://github.com/yiisoft/yii2/issues/1446
        if (empty($this->message['subject'])) {
            $this->message['subject'] = 'Application Log';
        }

        $message = Yii::$app->mailer->compose();
        Yii::configure($message, $this->message);
        $message->setTextBody($this->config['formattedMessage']);
        if (!$message->send($this->mailer)) {
            throw new LogRuntimeException('Unable to export log through email!');
        }
    }

    protected function prepareConfig()
    {
        $this->config['formattedMessage'] = $this->getFormatMessage();
    }
}