<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SystemLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="system-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'level') ?>

    <?= $form->field($model, 'category') ?>

    <?= $form->field($model, 'log_time') ?>

    <?= $form->field($model, 'prefix') ?>

    <?php // echo $form->field($model, 'message') ?>

    <?php // echo $form->field($model, 'text') ?>

    <?php // echo $form->field($model, 'user_agent') ?>

    <?php // echo $form->field($model, 'remote_ip') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
