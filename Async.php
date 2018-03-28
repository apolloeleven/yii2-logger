<?php
/**
 * Created by PhpStorm.
 * User: zura
 * Date: 3/28/18
 * Time: 1:48 PM
 */

namespace apollo11\logger;

use function GuzzleHttp\Psr7\str;
use Yii;
use yii\console\Controller;

class Async extends Controller
{
    public function actionEmail($param)
    {
        $from = Yii::$app->request->params[1];
        $to = Yii::$app->request->params[2];
        $subject = Yii::$app->request->params[3];
        $body = Yii::$app->request->params[4];

        EmailTarget::sendEmail($from, $to, $subject, $body);
    }
}