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
        ],
        /*'fieldConfig' => function ($model, $attribute) {
            if ($attribute == "files[]") {
                return ['options' => ['class' => 'form-group lead']];
            }
        }*/
    ]); ?>

    <?= $form->field($model, 'files[]', [
        'options' => [
            'class' => "upload-wrapper"
        ]
    ])->fileInput(['multiple' => true]) ?>

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
