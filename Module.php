<?php

namespace wdmg\media;

/**
 * Yii2 Media library
 *
 * @category        Module
 * @version         0.0.1
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-media
 * @copyright       Copyright (c) 2019 W.D.M.Group, Ukraine
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
    public $defaultRoute = "media/index";

    /**
     * @var string, the name of module
     */
    public $name = "Media";

    /**
     * @var string, the description of module
     */
    public $description = "Media library";

    /**
     * @var string the module version
     */
    private $version = "0.0.1";

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

    }

    /**
     * {@inheritdoc}
     */
    public function dashboardNavItems($createLink = false)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa-folder',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id])
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
}