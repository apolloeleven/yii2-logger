<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SystemLog */

$this->title = $model->category;
$this->params['breadcrumbs'][] = ['label' => 'System Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-log-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'level',
            'category',
            'log_time',
            'prefix:ntext',
            //'message:ntext',
            'user_agent:ntext',
            'remote_ip',
            [
                'attribute'=>'text',
                'format'=>'raw',
                'value'=>Html::tag('pre', $model->text, ['style'=>'white-space: pre-wrap'])
            ],
        ],
    ]) ?>
</div>
