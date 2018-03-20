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
    public $logTable = '{{%log}}';


    /**
     * Initializes the DbTarget component.
     * This method will initialize the [[db]] property to make sure it refers to a valid DB connection.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::class);
    }

    /**
     * Stores log messages to DB.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     * @throws LogRuntimeException
     */
    public function export()
    {
        if ($this->db->getTransaction()) {
            $this->db = clone $this->db;
        }

        $tableName = $this->db->quoteTableName($this->logTable);
        $sql = "INSERT INTO $tableName ([[level]], [[category]], [[log_time]], [[prefix]], [[message]])
                VALUES (:level, :category, :log_time, :prefix, :message)";
        $command = $this->db->createCommand($sql);
        foreach ($this->messages as $message) {
            list($text, $level, $category, $timestamp) = $message;
            if (!is_string($text)) {
                // exceptions may not be serializable if in the call stack somewhere is a Closure
                if ($text instanceof \Throwable || $text instanceof \Exception) {
                    $text = (string) $text;
                } else {
                    $text = VarDumper::export($text);
                }
            }
            if ($command->bindValues([
                    ':level' => $level,
                    ':category' => $category,
                    ':log_time' => $timestamp,
                    ':prefix' => $this->getMessagePrefix($message),
                    ':message' => $text,
                ])->execute() > 0) {
                continue;
            }
            throw new LogRuntimeException('Unable to export log through database!');
        }
    }
}