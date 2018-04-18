<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\SystemLog */

$this->title = 'Update System Log: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'System Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="system-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
