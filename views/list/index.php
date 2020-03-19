<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use wdmg\widgets\SelectInput;

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


                [
                    'attribute' => 'preview',
                    'format' => 'raw',
                    'value' => function($data) use ($module) {
                        $preview = '';
                        if ($mime = $module->getTypeByMime($data->mime_type)) {
                            if (isset($mime['type'])) {
                                if ($mime['type'] == 'image') {
                                    $preview = Html::tag('img', '', [
                                        'class' => 'img-thumbnail',
                                        'style' => 'width:64px;max-height:96px;',
                                        'src' => $data->getThumbnail(true, true)
                                    ]);
                                }
                            }
                        }

                        return $preview;

                    }
                ],
                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function($data) use ($module) {
                        $preview = '';
                        $output = Html::tag('strong', $data->name);
                        if (($mediaURL = $data->getMediaUrl(true, true)) && $data->id) {
                            $output .= '<br/>' . Html::a($data->url, $mediaURL, [
                                'target' => '_blank',
                                'data-pjax' => 0
                            ]);
                        }

                        return $output;
                    }
                ],

                /*'name',
                'alias',
                'path',*/
                /*'size',
                'title',
                'caption',
                'alt',
                'description',*/

                [
                    'attribute' => 'mime_type',
                    'format' => 'raw',
                    'filter' => SelectInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'mime_type',
                        'items' => $module->getMimeTypesList(true),
                        'options' => [
                            'class' => 'form-control'
                        ]
                    ]),
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) use ($module) {
                        if ($mime = $module->getTypeByMime($data->mime_type)) {
                            if (isset($mime['type'])) {
                                $type = $mime['type'];
                                $title = ((isset($mime['title'])) ? $mime['title'] : "");
                                $disabled = (($data->status == $data::MEDIA_STATUS_DRAFT) ? ' disabled="disabled"' : "");
                                if ($type == 'image') {
                                    return '<span class="label label-success" title="' . $title . '"' . $disabled . '>'.Yii::t('app/modules/media','Image').'</span>';
                                } elseif ($type == 'video') {
                                    return '<span class="label label-danger" title="' . $title . '"' . $disabled . '>'.Yii::t('app/modules/media','Video').'</span>';
                                } elseif ($type == 'audio') {
                                    return '<span class="label label-info" title="' . $title . '"' . $disabled . '>'.Yii::t('app/modules/media','Audio').'</span>';
                                } elseif ($type == 'document') {
                                    return '<span class="label label-warning" title="' . $title . '"' . $disabled . '>'.Yii::t('app/modules/media','Document').'</span>';
                                }
                            } else {
                                return '<span class="label label-warning" title="' . $title . '"' . $disabled . '>'.Yii::t('app/modules/media','Document').'</span>';
                            }
                        } else {
                            return '<span class="label label-default" title="' . $title . '"' .$disabled . '>'.Yii::t('app/modules/media','Unknown').'</span>';
                        }
                    }
                ],

                /*'params',*/
                /*'reference',*/
                [
                    'attribute' => 'categories',
                    'format' => 'html',
                    'filter' => SelectInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'categories',
                        'items' => $searchModel->getAllCategoriesList(true),
                        'options' => [
                            'id' => 'blogsearch-categories',
                            'class' => 'form-control'
                        ]
                    ]),
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) {
                        if ($categories = $data->getCategories()) {
                            $output = [];
                            foreach ($categories as $category) {
                                $output[] = Html::a($category->name, ['cats/view', 'id' => $category->id]);
                            }
                            return implode(", ", $output);
                        } else {
                            return null;
                        }
                    }
                ],
                [
                    'attribute' => 'status',
                    'format' => 'html',
                    'filter' => SelectInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'status',
                        'items' => $searchModel->getStatusesList(true),
                        'options' => [
                            'class' => 'form-control'
                        ]
                    ]),
                    'headerOptions' => [
                        'class' => 'text-center'
                    ],
                    'contentOptions' => [
                        'class' => 'text-center'
                    ],
                    'value' => function($data) {
                        if ($data->status == $data::MEDIA_STATUS_PUBLISHED)
                            return '<span class="label label-success">'.Yii::t('app/modules/media','Published').'</span>';
                        elseif ($data->status == $data::MEDIA_STATUS_DRAFT)
                            return '<span class="label label-default">'.Yii::t('app/modules/media','Draft').'</span>';
                        else
                            return $data->status;
                    }
                ],


                [
                    'attribute' => 'created',
                    'label' => Yii::t('app/modules/media','Uploaded by'),
                    'format' => 'html',
                    'value' => function($data) {

                        $output = "";
                        if ($user = $data->createdBy) {
                            $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                                'target' => '_blank',
                                'data-pjax' => 0
                            ]);
                        } else if ($data->created_by) {
                            $output = $data->created_by;
                        }

                        if (!empty($output))
                            $output .= ", ";

                        $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                        return $output;
                    }
                ],

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