<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model wdmg\media\models\Media */

$bundle = \wdmg\media\MediaAsset::register($this);

$this->title = Yii::t('app/modules/media', 'View media');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/media', 'Media library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="media-view">
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            [
                'attribute' => 'name',
                'value' => function($data) use ($module) {
                    return $data->name;
                }
            ],
            [
                'attribute' => 'preview',
                'label' => Yii::t('app/modules/media', 'Preview'),
                'format' => 'raw',
                'value' => function($data) use ($module) {
                    $preview = '';
                    if ($mime = $module->getTypeByMime($data->mime_type)) {
                        if (isset($mime['type'])) {
                            if ($mime['type'] == 'image') {
                                if ($thumnail = $data->getThumbnail(true, true)) {
                                    $preview = Html::tag('img', '', [
                                        'class' => 'img-thumbnail',
                                        'style' => 'width:360px;max-height:360px;',
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

                    return $preview;
                }
            ],
            [
                'attribute' => 'size',
                'value' => function($data) use ($module) {
                    $formatter = Yii::$app->formatter;
                    $formatter->sizeFormatBase = 1000;
                    return $formatter->asShortSize($data->size, 2);
                }
            ],
            [
                'attribute' => 'url',
                'label' => Yii::t('app/modules/media', 'Media URL'),
                'format' => 'raw',
                'value' => function($data) use ($module) {
                    if (($mediaURL = $data->getMediaUrl(true, true)) && $data->id) {
                        return Html::a($data->url, $mediaURL, [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    }

                    return $data->url;
                }
            ],
            [
                'attribute' => 'source',
                'label' => Yii::t('app/modules/media', 'Source'),
                'format' => 'raw',
                'value' => function($data) use ($module) {
                    if (($mediaURL = $data->getSource(true, true)) && $data->id) {
                        $url = Url::to($mediaURL, true);
                        return Html::a($url, $url, [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    }

                    return null;
                }
            ],

            [
                'attribute' => 'mime_type',
                'value' => function($data) use ($module) {
                    return $data->mime_type;
                }
            ],

            /*'params',*/
            /*'reference',*/

            [
                'attribute' => 'title',
                'value' => function($data) use ($module) {
                    return $data->title;
                }
            ],
            [
                'attribute' => 'caption',
                'value' => function($data) use ($module) {
                    return $data->caption;
                }
            ],
            [
                'attribute' => 'alt',
                'value' => function($data) use ($module) {
                    return $data->alt;
                }
            ],
            [
                'attribute' => 'description',
                'value' => function($data) use ($module) {
                    return $data->description;
                }
            ],

            [
                'attribute' => 'categories',
                'label' => Yii::t('app/modules/media', 'Category'),
                'format' => 'html',
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
                'label' => Yii::t('app/modules/media','Created'),
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
                'attribute' => 'updated',
                'label' => Yii::t('app/modules/media','Updated'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->updatedBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->updated_by) {
                        $output = $data->updated_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],
        ],
    ]); ?>
    <?php if ($exif = $model->getExifData()) : ?>
    <h3><?= Yii::t('app/modules/media', 'EXIF Data'); ?></h3>
    <?= DetailView::widget([
            'model' => $exif
        ]);
    endif; ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/media', '&larr; Back to list'), ['list/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
        <?php if (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
                'created_by' => $model->created_by,
                'updated_by' => $model->updated_by
            ])) : ?>
            <?= Html::a(Yii::t('app/modules/media', 'Update'), ['list/update', 'id' => $model->id], ['class' => 'btn btn-primary pull-right']) ?>
        <?php endif; ?>
    </div>
</div>