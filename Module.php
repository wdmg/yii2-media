<?php

namespace wdmg\media;

/**
 * Yii2 Media library
 *
 * @category        Module
 * @version         0.0.2
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-media
 * @copyright       Copyright (c) 2019 - 2020 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use Yii;
use wdmg\base\BaseModule;

/**
 * Media module definition class
 */
class Module extends BaseModule
{
    /**
     * {@inheritdoc}
     */
    public $controllerNamespace = 'wdmg\media\controllers';

    /**
     * {@inheritdoc}
     */
    public $defaultRoute = "list/index";

    /**
     * @var string, the name of module
     */
    public $name = "Media";

    /**
     * @var string, the description of module
     */
    public $description = "Media library";

    /**
     * @var string the default routes to render media (use "/" - for root)
     */
    public $mediaRoute = "/media";
    public $mediaCategoriesRoute = "/media/categories";

    /**
     * @var string, the default path to save media files in @webroot
     */
    public $mediaPath= "/uploads/media";

    /**
     * @var string the module version
     */
    private $version = "0.0.2";

    /**
     * @var integer, priority of initialization
     */
    private $priority = 5;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Set version of current module
        $this->setVersion($this->version);

        // Set priority of current module
        $this->setPriority($this->priority);


        // Process and normalize route for media in frontend
        $this->mediaRoute = self::normalizeRoute($this->mediaRoute);
        $this->mediaCategoriesRoute = self::normalizeRoute($this->mediaCategoriesRoute);

        // Normalize path for media folder
        $this->mediaPath = \yii\helpers\FileHelper::normalizePath($this->mediaPath);
    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($createLink = false)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa fa-fw fa-icons',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id]),
            'items' => [
                [
                    'label' => Yii::t('app/modules/media', 'Media list'),
                    'url' => [$this->routePrefix . '/media/list/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['content']) &&  Yii::$app->controller->id == 'list'),
                ],
                [
                    'label' => Yii::t('app/modules/media', 'Categories'),
                    'url' => [$this->routePrefix . '/media/cats/'],
                    'active' => (in_array(\Yii::$app->controller->module->id, ['content']) &&  Yii::$app->controller->id == 'cats'),
                ]
            ]
        ];
        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        parent::bootstrap($app);
    }


    /**
     * {@inheritdoc}
     */
    public function install()
    {
        parent::install();
        $path = Yii::getAlias('@webroot') . $this->mediaPath;
        if (\yii\helpers\FileHelper::createDirectory($path, $mode = 0775, $recursive = true))
            return true;
        else
            return false;
    }

    /**
     * {@inheritdoc}
     */
    public function uninstall()
    {
        parent::uninstall();
        $path = Yii::getAlias('@webroot') . $this->mediaPath;
        if (\yii\helpers\FileHelper::removeDirectory($path))
            return true;
        else
            return false;
    }
}