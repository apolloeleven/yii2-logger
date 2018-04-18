<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SystemLog */

$this->title = 'Create System Log';
$this->params['breadcrumbs'][] = ['label' => 'System Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
