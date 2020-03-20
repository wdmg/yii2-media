<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\media\models\Media */

\yii\web\YiiAsset::register($this);

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
    <?= $form->field($model, 'cat_id')->widget(SelectInput::class, [
        'model' => $model,
        'attribute' => 'cat_id',
        'items' => $model->getCategoriesList(),
        'options' => [
            'class' => 'form-control'
        ]
    ])->label(Yii::t('app/modules/media', 'Category')); ?>
    <?= $form->field($model, 'files[]')->fileInput(['multiple' => true]) ?>
    <div class="row">
        <div class="modal-footer" style="clear:both;display:inline-block;width:100%;padding-bottom:0;">
            <?= Html::a(Yii::t('app/modules/media', 'Close'), "#", [
                'class' => 'btn btn-default pull-left',
                'data-dismiss' => 'modal'
            ]) ?>
            <?= Html::submitButton(Yii::t('app/modules/media', 'Upload'), ['class' => 'btn btn-success pull-right']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
