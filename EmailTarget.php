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

    /**
     * @var array
     */
    public $message = [];

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
        if (empty($this->message['subject'])) $this->message['subject'] = 'Error Log';
        if (empty($this->message['from'])) $this->message['from'] = 'test@test.test';

        Yii::$app->mailer->compose()
            ->setFrom($this->message['from'])
            ->setTextBody($this->getFormatMessage())
            ->setTo($this->message['to'])
            ->setSubject($this->message['subject'])
            ->send();
    }
}