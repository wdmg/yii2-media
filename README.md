[![Yii2](https://img.shields.io/badge/required-Yii2_v2.0.33-blue.svg)](https://packagist.org/packages/yiisoft/yii2)
[![Github all releases](https://img.shields.io/github/downloads/wdmg/yii2-media/total.svg)](https://GitHub.com/wdmg/yii2-media/releases/)
![Progress](https://img.shields.io/badge/progress-in_development-red.svg)
[![GitHub license](https://img.shields.io/github/license/wdmg/yii2-media.svg)](https://github.com/wdmg/yii2-media/blob/master/LICENSE)
![GitHub release](https://img.shields.io/github/release/wdmg/yii2-media/all.svg)

# Yii2 Media
Media library for Yii2

# Requirements 
* PHP 5.6 or higher
* Yii2 v.2.0.33 and newest
* [Yii2 Base](https://github.com/wdmg/yii2-base) module (required)
* [Yii2 Imagine](https://github.com/yiisoft/yii2-imagine) extension (required)
* [Yii2 SelectInput](https://github.com/wdmg/yii2-selectinput) widget

# Installation
To install the module, run the following command in the console:

`$ composer require "wdmg/yii2-media"`

After configure db connection, run the following command in the console:

`$ php yii media/init`

And select the operation you want to perform:
  1) Apply all module migrations
  2) Revert all module migrations

# Migrations
In any case, you can execute the migration and create the initial data, run the following command in the console:

`$ php yii migrate --migrationPath=@vendor/wdmg/yii2-media/migrations`

# Configure
To add a module to the project, add the following data in your configuration file:

    'modules' => [
        ...
        'media' => [
            'class' => 'wdmg\media\Module',
            'routePrefix' => 'admin',
            'mediaRoute' => '/media', // Routes to render media item (use "/" - for root)
            'mediaCategoriesRoute' => '/media/categories', // Routes to render media categories (use "/" - for root)
            'mediaPath' => '/uploads/media', // Path to save media files in @webroot
            'mediaThumbsPath' => '/uploads/media/_thumbs', // Path to save media thumbnails in @webroot
            'maxFilesToUpload' => 10, // maximum files to upload
            'allowedMime' => [ // allowed mime types
                'image/png' => true,
                'image/jpeg' => true,
                'image/gif' => true,
                'image/svg+xml' => true,
                'application/pdf' => true,
                'application/msword' => true,
                'application/vnd.ms-excel' => true,
                'application/rtf' => true,
                'text/csv' => true,
                'text/plain' => true,
                ...
            ]
        ],
        ...
    ],


# Routing
Use the `Module::dashboardNavItems()` method of the module to generate a navigation items list for NavBar, like this:

    <?php
        echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
            'label' => 'Modules',
            'items' => [
                Yii::$app->getModule('media')->dashboardNavItems(),
                ...
            ]
        ]);
    ?>

# Status and version [in progress development]
* v.1.0.2 - Added pagination, up to date dependencies
* v.1.0.1 - Check file not exists or generate unique filename
* v.1.0.0 - CRUD for media items/categories, translations
* v.0.0.3 - Upload functionality, mime types validator and preview thumbnails