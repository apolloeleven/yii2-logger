<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace apollo11\logger;

use yii\helpers\ArrayHelper;
use yii\helpers\Console;
use yii\helpers\VarDumper;

/**
 * Target is the base class for all log target classes.
 *
 * A log target object will filter the messages logged by [[Logger]] according
 * to its [[levels]] and [[categories]] properties. It may also export the filtered
 * messages to specific destination defined by the target, such as emails, files.
 *
 * Level filter and category filter are combinatorial, i.e., only messages
 * satisfying both filter conditions will be handled. Additionally, you
 * may specify [[except]] to exclude messages of certain categories.
 *
 * @property bool $enabled Indicates whether this log target is enabled. Defaults to true. Note that the type
 * of this property differs in getter and setter. See [[getEnabled()]] and [[setEnabled()]] for details.
 * @property int $levels The message levels that this target is interested in. This is a bitmap of level
 * values. Defaults to 0, meaning  all available levels. Note that the type of this property differs in getter
 * and setter. See [[getLevels()]] and [[setLevels()]] for details.
 *
 * For more details and usage information on Target, see the [guide article on logging & targets](guide:runtime-logging).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Target extends \yii\log\Target
{
    /**
     * @var
     */
    public $excludeKeys;

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

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        parent::init();
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
}
