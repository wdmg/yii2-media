<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\media\models\Media */
/* @var $form yii\widgets\ActiveForm */

\wdmg\media\FontAwesomeAssets::register($this);
$bundle = \wdmg\media\MediaAsset::register($this);

$this->title = Yii::t('app/modules/media', 'Upload media');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/media', 'Media library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/media', 'Upload');

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="media-upload">
    <?php $form = ActiveForm::begin([
        'id' => "uploadNewMediaForm",
        'action' => Url::to(['list/upload']),
        'method' => 'post',
        'options' => [
            'enctype' => 'multipart/form-data'
        ]
    ]); ?>

    <?= $form->field($model, 'files[]', [
        'options' => [
            'class' => "upload-wrapper"
        ]
    ])->label(Yii::t('app/modules/media', 'Select files'), [
        'data' => [
            'label' => Yii::t('app/modules/media', 'Drag and drop a files here or click for select')
        ]
    ])->fileInput(['multiple' => true]) ?>

    <p class="alert alert-info">
        <?= Yii::t('app/modules/media', 'Maximum upload file size: {size}.', [
            'size' => $model->getMaxUploadFilesize(true)
        ]); ?>
    </p>

    <?= $form->field($model, 'cat_id')->widget(SelectInput::class, [
        'model' => $model,
        'attribute' => 'cat_id',
        'items' => $model->getCategoriesList(),
        'options' => [
            'class' => 'form-control'
        ]
    ])->label(Yii::t('app/modules/media', 'Category')); ?>
    <hr/>
    <div class="form-group">
        <?= Html::a(Yii::t('app/modules/media', '&larr; Back to list'), ['list/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
    </div>
    <?php ActiveForm::end(); ?>
</div>


