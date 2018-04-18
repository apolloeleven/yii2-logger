<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\SystemLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'System Logs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create System Log', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'level',
            'category',
            'log_time',
            'prefix:ntext',
            //'message:ntext',
            //'text:ntext',
            //'user_agent:ntext',
            //'remote_ip',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
