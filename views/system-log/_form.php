<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SystemLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="system-log-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'level')->textInput() ?>

    <?= $form->field($model, 'category')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'log_time')->textInput() ?>

    <?= $form->field($model, 'prefix')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'message')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'text')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'user_agent')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'remote_ip')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
