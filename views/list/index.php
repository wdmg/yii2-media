<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */

$this->title = Yii::t('app/modules/media', 'Media library');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
    <div class="media-list-index">

        <?php Pjax::begin(); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'cat_id',
                'name',
                'alias',
                'path',
                'size',
                'title',
                'caption',
                'alt',
                'description',
                'mime_type',
                'params',
                'reference',
                'status',
                'created_at',
                'created_by',
                'updated_at',
                'updated_by',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app/modules/media','Actions'),
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                ]
            ]
        ]); ?>
        <hr/>
        <div>
            <?= Html::a(Yii::t('app/modules/media', 'Add new media'), ['list/upload'], [
                'class' => 'btn btn-success pull-right',
                'data-toggle' => 'modal',
                'data-target' => '#uploadNewMedia',
                'data-pjax' => '1'
            ]) ?>
        </div>
        <?php Pjax::end(); ?>
    </div>

<?php $this->registerJs(<<< JS
    $('body').delegate('#uploadNewMedia', 'hidden.bs.modal', function(event) {
        $(this).find('#uploadNewMediaForm')[0].reset();
    });
JS
); ?>

<?php Modal::begin([
    'id' => 'uploadNewMedia',
    'size' => 'modal-lg',
    'header' => '<h4 class="modal-title">'.Yii::t('app/modules/media', 'Upload new media').'</h4>',
    'clientOptions' => [
        'show' => false
    ]
]); ?>
<?php echo $this->render('_upload', ['model' => $model]); ?>
<?php Modal::end(); ?>

<?php echo $this->render('../_debug'); ?>