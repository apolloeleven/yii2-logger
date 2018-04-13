<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace apollo11\logger;

use yii\db\Connection;
use yii\di\Instance;
use yii\helpers\VarDumper;
use yii\log\LogRuntimeException;


class DbTarget extends Target
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the DbTarget object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     */
    public $db = 'db';

    /**
     * @var string name of the DB table to store cache content. Defaults to "log".
     */
    public $logTable = '{{%apollo11_sys_log}}';


    /**
     * Initializes the DbTarget component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
    }

    public function sendMessage()
    {

        if ($this->db->getTransaction()) {
            $this->db = clone $this->db;
        }

        if (\Yii::$app->db->schema->getTableSchema($this->logTable) !== null) {
            $tableName = $this->db->quoteTableName($this->logTable);
            $sql = "INSERT INTO $tableName ([[level]], [[category]], [[log_time]], [[prefix]], [[message]],[[text]],[[user_agent]],[[remote_ip]])
                VALUES (:level, :category, :log_time, :prefix, :message,:text,:user_agent,:remote_ip)";
            $command = $this->db->createCommand($sql);
            foreach ($this->config['messages'] as $message) {
                if ($command->bindValues([
                        ':level' => $message['level'],
                        ':category' => $message['category'],
                        ':log_time' => $message['timestamp'],
                        ':prefix' => $message['prefix'],
                        ':message' => $message['text'],
                        ':text' => $this->config['formattedMessage'],
                        ':user_agent' => $this->config['user_agent'],
                        ':remote_ip' => $this->config['remote_ip'],
                    ])->execute() > 0) {
                    continue;
                }
                throw new LogRuntimeException('Unable to export log through database!');
            }
        } else {
            throw new LogRuntimeException('Table ' . $this->logTable . ' Does not exist');
        }
    }

    protected function prepareConfig()
    {
        $messages = [];
        foreach ($this->messages as $key => $message) {
            list($text, $level, $category, $timestamp) = $message;
            if (!is_string($text)) {
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = (string)$text;
                } else {
                    $text = VarDumper::export($text);
                }
            }

            $messages[$key]['text'] = $text;
            $messages[$key]['level'] = $level;
            $messages[$key]['category'] = $category;
            $messages[$key]['timestamp'] = $timestamp;
            $messages[$key]['prefix'] = $this->getMessagePrefix($message);
        }

        $this->config = [
            'messages' => $messages,
            'formattedMessage' => $this->getFormatMessage(),
            'user_agent' => \Yii::$app->request->getUserAgent(),
            'remote_ip' => \Yii::$app->request->getRemoteIP(),
        ];
    }
}
