<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wdmg\widgets\SelectInput;

/* @var $this yii\web\View */
/* @var $model wdmg\media\models\Media */
/* @var $form yii\widgets\ActiveForm */

$bundle = \wdmg\media\MediaAsset::register($this);

$this->title = Yii::t('app/modules/media', 'Updating media: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/media', 'Media library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/media', 'Edit');

?>
<?php if (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && Yii::$app->user->can('updatePosts', [
        'created_by' => $model->created_by,
        'updated_by' => $model->updated_by
    ])) : ?>
    <div class="page-header">
        <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
    </div>
    <div class="media-update">
        <div class="media-form">
            <?php $form = ActiveForm::begin([
                'id' => "updateMediaForm",
                'enableAjaxValidation' => true,
                'options' => [
                    'enctype' => 'multipart/form-data'
                ]
            ]); ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

            <?php
            $output = '';
            if (($mediaURL = $model->getMediaUrl(true, true)) && $model->id) {
                $output = Html::a($model->getMediaUrl(true, false), $mediaURL, [
                    'target' => '_blank',
                    'data-pjax' => 0
                ]);
            }

            if (!empty($output))
                echo Html::tag('label', Yii::t('app/modules/media', 'Media URL')) . Html::tag('fieldset', $output) . '<br/>';

            ?>

            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'title')->textInput() ?>
            <?= $form->field($model, 'caption')->textInput() ?>
            <?= $form->field($model, 'alt')->textInput() ?>
            <?= $form->field($model, 'description')->textarea(['rows' => 3]) ?>

            <?= $form->field($model, 'cat_id')->widget(SelectInput::class, [
                'items' => $model->getAllCategoriesList(false),
                'options' => [
                    'class' => 'form-control'
                ]
            ])->label(Yii::t('app/modules/media', 'Category')); ?>

            <?= $form->field($model, 'status')->widget(SelectInput::class, [
                'items' => $model->getStatusesList(),
                'options' => [
                    'class' => 'form-control'
                ]
            ]); ?>

            <hr/>
            <div class="form-group">
                <?= Html::a(Yii::t('app/modules/media', '&larr; Back to list'), ['list/index'], ['class' => 'btn btn-default pull-left']) ?>&nbsp;
                <?= Html::submitButton(Yii::t('app/modules/media', 'Save'), ['class' => 'btn btn-success pull-right']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <?php $this->registerJs(<<< JS
$(document).ready(function() {
    function afterValidateAttribute(event, attribute, messages)
    {
        if (attribute.name && !attribute.alias && messages.length == 0) {
            var form = $(event.target);
            $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serializeArray(),
                }
            ).done(function(data) {
                if (data.alias && form.find('#media-alias').val().length == 0) {
                    form.find('#media-alias').val(data.alias);
                    form.yiiActiveForm('validateAttribute', 'media-alias');
                }
            }).fail(function () {
                /*form.find('#options-type').val("");
                form.find('#options-type').trigger('change');*/
            });
            return false; // prevent default form submission
        }
    }
    $("#updateMediaForm").on("afterValidateAttribute", afterValidateAttribute);
});
JS
    ); ?>
<?php else : ?>
    <div class="page-header">
        <h1 class="text-danger"><?= Yii::t('app/modules/media', 'Error {code}. Access Denied', [
                'code' => 403
            ]) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
    </div>
    <div class="media-update-error">
        <blockquote>
            <?= Yii::t('app/modules/media', 'You are not allowed to view this page.'); ?>
        </blockquote>
    </div>
<?php endif; ?>
