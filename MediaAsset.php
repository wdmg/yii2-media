<?php

namespace wdmg\media;
use yii\web\AssetBundle;

class MediaAsset extends AssetBundle
{
    public $sourcePath = '@vendor/wdmg/yii2-media/assets';

    public $css = [
        YII_ENV_DEV ? 'css/media.css' : 'css/media.min.css'
    ];

    public $js = [
        YII_ENV_DEV ? 'js/media.js' : 'js/media.min.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    public function init()
    {
        parent::init();
    }
}

?>