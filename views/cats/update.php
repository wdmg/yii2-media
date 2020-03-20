<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model wdmg\media\models\Categories */

$this->title = Yii::t('app/modules/media', 'Updating category: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/media', 'Media library'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/media', 'All categories'), 'url' => ['cats/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/media', 'Edit');


?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?><?= ($model->id === 1) ? " <span class=\"text-muted\">(" . Yii::t('app/modules/media', 'default') . ")</span>" : ""?><small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="media-cats-update">
    <?= $this->render('_form', [
        'module' => $module,
        'model' => $model,
        'parentsList' => $model->getParentsList(false, true)
    ]); ?>
</div>