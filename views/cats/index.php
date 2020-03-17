<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */

$this->title = Yii::t('app/modules/media', 'Media categories');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
    <div class="media-cats-index">

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                
                'parent_id',
                'name',
                'alias',
                'title',
                'description',
                'keywords',
                'created_at',
                'created_by',
                'updated_at',
                'updated_by',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app/modules/blog','Actions'),
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'visibleButtons' => [
                        'delete' => function ($data) {
                            return !($data->id === $data::DEFAULT_CATEGORY_ID); // Category for uncategorized posts has undeleted
                        },
                    ]
                ]
            ]
        ]); ?>
        <hr/>
        <div>
            <?= Html::a(Yii::t('app/modules/media', 'Add new category'), ['cats/create'], ['class' => 'btn btn-success pull-right']) ?>
        </div>
        <?php Pjax::end(); ?>
    </div>

<?php echo $this->render('../_debug'); ?>