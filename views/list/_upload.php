<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\media\models\Media */

\wdmg\media\FontAwesomeAssets::register($this);

?>
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
    <div class="row">
        <div class="modal-footer" style="clear:both;display:inline-block;width:100%;padding-bottom:0;">
            <?= Html::a(Yii::t('app/modules/media', 'Close'), "#", [
                'class' => 'btn btn-danger pull-right',
                'data-dismiss' => 'modal'
            ]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
