<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */

$bundle = \wdmg\media\MediaAsset::register($this);

$this->title = Yii::t('app/modules/media', 'Media library');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
    <div class="media-list-index">

        <?php Pjax::begin([
            'id' => "pageContainer"
        ]); ?>
        <?= GridView::widget([
            'id' => "mediaList",
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'checkboxOptions' => function($model) {
                        return [
                            'value' => $model->id
                        ];
                    }
                ],

                [
                    'attribute' => 'name',
                    'format' => 'raw',
                    'value' => function($data) use ($module) {
                        $preview = '';
                        if ($mime = $module->getTypeByMime($data->mime_type)) {
                            if (isset($mime['type'])) {
                                if ($mime['type'] == 'image') {
                                    if ($thumnail = $data->getThumbnail(true, true)) {
                                        $preview = Html::tag('img', '', [
                                            'class' => 'media-object img-thumbnail',
                                            'style' => 'width:64px;max-height:96px;',
                                            'src' => $thumnail
                                        ]);
                                    } else {
                                        $preview = Html::tag('span', '', [
                                            'class' => 'media-object icon icon-filetype ' . $mime['icon']
                                        ]);
                                    }
                                } else {
                                    $preview = Html::tag('span', '', [
                                        'class' => 'media-object icon icon-filetype ' . $mime['icon']
                                    ]);
                                }
                            } else {
                                $preview = Html::tag('span', '', [
                                    'class' => 'media-object icon icon-filetype icon-unknown'
                                ]);
                            }
                        } else {
                            $preview = Html::tag('span', '', [
                                'class' => 'media-object icon icon-filetype icon-unknown'
                            ]);
                        }

                        $output = Html::tag('strong', $data->name);

                        if ($data->size) {
                            $formatter = Yii::$app->formatter;
                            $formatter->sizeFormatBase = 1000;
                            $output .= '<br/>' . Html::tag('em', $formatter->asShortSize($data->size, 2), [
                                'class' => "text-muted"
                            ]);
                        }

                        if (($mediaURL = $data->getMediaUrl(true, true)) && $data->id) {
                            $output .= '<br/>' . Html::a($data->url, $mediaURL, [
                                'target' => '_blank',
                                'data-pjax' => 0
                            ]);
                        }

                        if (!empty($preview))
                            return '<div class="media"><div class="media-left">' .$preview . '</div><div class="media-body">' . $output . '</div></div>';

                        return $output;
                    }
                ],
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
                        $disabled = (($data->status == $data::MEDIA_STATUS_DRAFT) ? ' disabled="disabled"' : "");
                        if ($mime = $module->getTypeByMime($data->mime_type)) {
                            if (isset($mime['type'])) {
                                $type = $mime['type'];
                                $title = ((isset($mime['title'])) ? $mime['title'] : "");
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
                                return '<span class="label label-warning" title="Unknown"' . $disabled . '>'.Yii::t('app/modules/media','Document').'</span>';
                            }
                        } else {
                            return '<span class="label label-default" title="Unknown"' .$disabled . '>'.Yii::t('app/modules/media','Unknown').'</span>';
                        }
                    }
                ],
                [
                    'attribute' => 'cat_id',
                    'label' => Yii::t('app/modules/media', 'Category'),
                    'format' => 'html',
                    'filter' => SelectInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'cat_id',
                        'items' => $searchModel->getAllCategoriesList(true),
                        'options' => [
                            'id' => 'media-search-categories',
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
                    'contentOptions' => [
                        'class' => 'text-center',
                        'style' => 'min-width:146px'
                    ],
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
            <div class="btn-group">
                <?= Html::button(Yii::t('app/modules/media', 'Select action') . ' <span class="caret"></span>', [
                    'id' => 'batchSelectAction',
                    'class' => 'btn btn-default dropdown-toggle',
                    'data-toggle' => 'dropdown',
                    'aria-haspopup' => 'true',
                    'aria-expanded' => 'false',
                    'disabled' => 'disabled',
                    'data-pjax' => '0'
                ]) ?>
                <ul class="dropdown-menu">
                    <?php
                        $categories = $model->getStatusesList(false);
                        if ($categories) {
                            foreach ($categories as $key => $name) {
                                echo "<li>" . Html::a(Yii::t('app/modules/media', 'Change status to: {name}', [
                                        'name' => $name
                                    ]), [
                                        'list/batch',
                                        'action' => 'change',
                                        'attribute' => 'status',
                                        'value' => $key,
                                    ], [
                                        'id' => 'changeStatusSelected',
                                        'data-method' => 'POST',
                                        'data-pjax' => '0'
                                    ]) . "</li>";
                            }
                        }
                    ?>
                    <li role="separator" class="divider"></li>
                    <?php
                        $categories = $model->getAllCategoriesList(false);
                        if ($categories) {
                            foreach ($categories as $key => $name) {
                                echo "<li>" . Html::a(Yii::t('app/modules/media', 'Change category to: {name}', [
                                        'name' => $name
                                    ]), [
                                        'list/batch',
                                        'action' => 'change',
                                        'attribute' => 'cat_id',
                                        'value' => $key,
                                    ], [
                                        'id' => 'changeCategorySelected',
                                        'data-method' => 'POST',
                                        'data-pjax' => '0'
                                    ]) . "</li>";
                            }
                        }
                    ?>
                    <li role="separator" class="divider"></li>
                    <li>
                        <?= Html::a(Yii::t('app/modules/media', 'Delete selected'), [
                            'list/batch',
                            'action' => 'delete'
                        ], [
                            'id' => 'batchDeleteSelected',
                            'class' => 'bg-danger text-danger',
                            'data-pjax' => '0'
                        ]) ?>
                    </li>
                </ul>
            </div>
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
    $('body').delegate('#mediaList input[type="checkbox"]', 'click', function(event) {
        setTimeout(function() {
            var selected = $('#mediaList').yiiGridView('getSelectedRows');
        if (selected.length) {
            $('#batchSelectAction').removeAttr('disabled');
        } else {
            $('#batchSelectAction').attr('disabled', 'disabled');
        }
        }, 300);
    });
    $('body').delegate('#changeStatusSelected, #changeCategorySelected, #batchDeleteSelected', 'click', function(event) {
        event.preventDefault();
        var url = $(event.target).attr('href');
        var selected = $('#mediaList').yiiGridView('getSelectedRows');
        if (selected.length) {
            $.post({
                url: url,
                data: {selected: selected},
                success: function(data) {
                    $.pjax({
                        container: "#pageContainer"
                    });
                },
                error:function(erorr, responseText, code) {
                    window.location.reload();
                }
            });
        }
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