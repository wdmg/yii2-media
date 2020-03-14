<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Yii::t('app/modules/media', 'Media library');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="page-header">
    <h1><?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small></h1>
</div>
<div class="media-index">

</div>

<?php echo $this->render('../_debug'); ?>