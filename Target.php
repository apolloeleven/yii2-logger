<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace apollo11\logger;

use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

/**
 * @inheritdoc
 */
abstract class Target extends \yii\log\Target
{
    /**
     * @author Zura Sekhniashvili <zurasekhniashvili@gmail.com>
     * @var array Config array which should be configured in `->prepareConfig` method and used in `->sendMessage` method
     */
    protected $config = [];

    /**
     * @author Zura Sekhniashvili <zurasekhniashvili@gmail.com>
     * @var bool Whether to send messages to target asynchronously
     */
    public $async = true;

    /**
     * @author Zura Sekhniashvili <zurasekhniashvili@gmail.com>
     * @var bool
     */
    public $consoleAppPath = false;

    /**
     * @author Zura Sekhniashvili <zurasekhniashvili@gmail.com>
     * @var string php executable
     */
    public $phpExecPath = 'php';

    /**
     * @var
     */
    public $excludeKeys = [];

    /**
     * Generates the context information to be logged.
     * The default implementation will dump user information, system variables, etc.
     * @return string the context information. If an empty string, it means no context information.
     */
    protected function getContextMessage()
    {
        $context = ArrayHelper::filter($GLOBALS, $this->logVars);
        $result = [];
        foreach ($context as $key => $value) {
            $value = $this->replaceMustExcludeKeys($value);
            $result[] = "\${$key} = " . VarDumper::dumpAsString($value);
        }

        return implode("\n\n", $result);
    }

    /**
     * @param $key
     * @return bool
     */
    private function mustBeExcluded($key)
    {
        foreach ($this->excludeKeys as $excludeKey) {

            $formattedExcludeKey = $this->clearFromStars($excludeKey);
            $loweredKey = strtolower($key);

            if ($excludeKey[0] === '*' && substr($excludeKey, -1) === '*' && strpos($loweredKey, $formattedExcludeKey) !== false
                || $excludeKey[0] === '*' && preg_match('/' . $formattedExcludeKey . '$/', $loweredKey)
                || substr($excludeKey, -1) === '*' && preg_match('/^' . $formattedExcludeKey . '/', $loweredKey)
                || $loweredKey === $formattedExcludeKey) {
                return true;
            }

        }

        return false;
    }

    public function replaceMustExcludeKeys($data)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->replaceMustExcludeKeys($value);
            } else if ($this->mustBeExcluded($key)) {
                $data[$key] = str_repeat("*", strlen($value));
            }
        }
        return $data;
    }

    public function getFormatMessage()
    {
        $messages = array_map([$this, 'formatMessage'], $this->messages);
        return wordwrap(implode("\n", $messages), 70);
    }

    public function clearFromStars($excludeKey)
    {
        return strtolower(str_replace("*", "", $excludeKey));
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        $this->prepareConfig();
        $this->messages = [];
        if ($this->async === true && $this->consoleAppPath !== false) {
            $param = base64_encode(serialize($this));
            $cmd = $this->phpExecPath . ' ' . $this->consoleAppPath . " async/handle $param > /dev/null 2>/dev/null &";
            exec($cmd);
        } else {
            $this->sendMessage();
        }
    }

    abstract public function sendMessage();

    abstract protected function prepareConfig();
}
