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

class AsyncController extends Controller
{
    public function actionHandle()
    {
        $target = (unserialize(base64_decode((Yii::$app->request->params[1]))));
        $target->sendMessage();
    }
}