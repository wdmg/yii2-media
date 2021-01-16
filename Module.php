<?php

namespace wdmg\media;

/**
 * Yii2 Media library
 *
 * @category        Module
 * @version         1.0.6
 * @author          Alexsander Vyshnyvetskyy <alex.vyshnyvetskyy@gmail.com>
 * @link            https://github.com/wdmg/yii2-media
 * @copyright       Copyright (c) 2019 - 2021 W.D.M.Group, Ukraine
 * @license         https://opensource.org/licenses/MIT Massachusetts Institute of Technology (MIT) License
 *
 */

use wdmg\helpers\StringHelper;
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
     * @var string the module version
     */
    private $version = "1.0.6";

    /**
     * @var string the default routes to render media item (use "/" - for root)
     */
    public $mediaRoute = "/media";

    /**
     * @var string the default routes to render media categories (use "/" - for root)
     */
    public $mediaCategoriesRoute = "/media/categories";

    /**
     * @var string, the default path to save media files in @webroot
     */
    public $mediaPath = "/uploads/media";

    /**
     * @var string, the default path to save media thumbnails in @webroot
     */
    public $mediaThumbsPath = "/uploads/media/_thumbs";

    /**
     * @var int, maximum files to upload
     */
    public $maxFilesToUpload = 10;

    /**
     * @var int, max file size in bytes to upload
     */
    public $maxUploadSize = 5242880;

    /**
     * @var array, allowed mime types
     */
    public $allowedMime = [
        'image/png' => true,
        'image/jpeg' => true,
        'image/gif' => true,
        'image/tiff' => false,
        'image/svg+xml' => true,
        'image/webp' => false,
        'image/bmp' => false,
        'video/mpeg' => true,
        'video/mp4' => true,
        'video/ogg' => false,
        'video/quicktime' => true,
        'video/webm' => true,
        'video/x-matroska' => false,
        'video/x-ms-wmv' => true,
        'video/vnd.avi' => true,
        'audio/mpeg' => true,
        'audio/webm' => false,
        'audio/ogg' => false,
        'audio/mp4' => false,
        'audio/aac' => false,
        'audio/x-matroska' => false,
        'audio/x-ms-wma' => false,
        'audio/vnd.wave' => false,
        'audio/vnd.rn-realaudio' => false,
        'application/pdf' => true,
        'application/msword' => true,
        'application/vnd.oasis.opendocument.text' => false,
        'application/vnd.ms-excel' => true,
        'application/vnd.oasis.opendocument.spreadsheet' => false,
        'application/vnd.ms-powerpoint' => false,
        'application/vnd.oasis.opendocument.presentation' => false,
        'application/rtf' => true,
        'application/json' => true,
        'text/csv' => true,
        'text/plain' => true,
    ];

    /**
     * @var array, mime types collection of support
     */
    private $mimeTypes = [
        'image/png' => [
            'icon' => 'icon-image-png',
            'title' => 'PNG',
            'description' => 'Portable Network Graphics',
            'extensions' => ['.png', '.apng'],
            'type' => 'image'
        ],
        'image/jpeg' => [
            'icon' => 'icon-image-jpeg',
            'title' => 'JPEG',
            'description' => 'Joint Photographic Experts Group Format',
            'extensions' => ['.jpg', '.jpeg', '.jpe', '.jfif'],
            'type' => 'image'
        ],
        'image/gif' => [
            'icon' => 'icon-image-gif',
            'title' => 'GIF',
            'description' => 'Graphics Interchange Format',
            'extensions' => ['.gif'],
            'type' => 'image'
        ],
        'image/tiff' => [
            'icon' => 'icon-image-tiff',
            'title' => 'TIFF',
            'description' => 'Tagged Image File Format',
            'extensions' => ['.tif', '.tiff'],
            'type' => 'image'
        ],
        'image/svg+xml' => [
            'icon' => 'icon-image-svg',
            'title' => 'SVG',
            'description' => 'Scalable Vector Graphics',
            'extensions' => ['.svg', '.svgz'],
            'type' => 'image'
        ],
        'image/webp' => [
            'icon' => 'icon-image-webp',
            'title' => 'WebP',
            'description' => 'Web Picture Format',
            'extensions' => ['.webp'],
            'type' => 'image'
        ],
        'image/bmp' => [
            'icon' => 'icon-image-bmp',
            'title' => 'BMP',
            'description' => 'Windows OS/2 Bitmap Graphics',
            'extensions' => ['.bmp'],
            'type' => 'image'
        ],
        'video/mpeg' => [
            'icon' => 'icon-video-mpg',
            'title' => 'MPEG',
            'description' => 'Moving Picture Experts Group',
            'extensions' => ['.mpg', '.mpeg', '.mpv'],
            'type' => 'video'
        ],
        'video/mp4' => [
            'icon' => 'icon-video-mp4',
            'title' => 'MP4',
            'description' => 'MPEG-4 Part 14 Video Format',
            'extensions' => ['.mp4'],
            'type' => 'video'
        ],
        'video/ogg' => [
            'icon' => 'icon-video-ogv',
            'title' => 'OGG',
            'description' => 'Ogg Theora',
            'extensions' => ['.ogv', '.ogg'],
            'type' => 'video'
        ],
        'video/quicktime' => [
            'icon' => 'icon-video-mov',
            'title' => 'QuickTime',
            'description' => 'QuickTime Media Format',
            'extensions' => ['.mov'],
            'type' => 'video'
        ],
        'video/webm' => [
            'icon' => 'icon-video-webm',
            'title' => 'WebM',
            'description' => 'Web Media Video Format',
            'extensions' => ['.webm'],
            'type' => 'video'
        ],
        'video/x-ms-wmv' => [
            'icon' => 'icon-video-wmv',
            'title' => 'WMV',
            'description' => 'Windows Media Video',
            'extensions' => ['.wmv'],
            'type' => 'video'
        ],
        'video/vnd.avi' => [
            'icon' => 'icon-video-avi',
            'title' => 'AVI',
            'description' => 'Audio Video Interleave',
            'extensions' => ['.avi'],
            'type' => 'video'
        ],
        'video/x-matroska' => [
            'icon' => 'icon-video-mkv',
            'title' => 'MKV',
            'description' => 'Matroska Video File',
            'extensions' => ['.mkv'],
            'type' => 'video'
        ],
        'audio/mpeg' => [
            'icon' => 'icon-audio-mp3',
            'title' => 'MP3',
            'description' => 'MPEG Layer 3',
            'extensions' => ['.mp3'],
            'type' => 'audio'
        ],
        'audio/webm' => [
            'icon' => 'icon-audio-weba',
            'title' => 'WebM Audio',
            'description' => 'Web Media Audio Format',
            'extensions' => ['.weba'],
            'type' => 'audio'
        ],
        'audio/ogg' => [
            'icon' => 'icon-audio-ogg',
            'title' => 'Ogg Audio',
            'description' => 'Ogg Vorbis Audio Format',
            'extensions' => ['.ogg', '.oga', '.sb0'],
            'type' => 'audio'
        ],
        'audio/mp4' => [
            'icon' => 'icon-audio-mp4',
            'title' => 'MP4',
            'description' => 'MPEG-4 Part 14 Audio Format',
            'extensions' => ['.mp4'],
            'type' => 'audio'
        ],
        'audio/aac' => [
            'icon' => 'icon-audio-aac',
            'title' => 'AAC',
            'description' => 'Advanced Audio Coding',
            'extensions' => ['.aac'],
            'type' => 'audio'
        ],
        'audio/x-ms-wma' => [
            'icon' => 'icon-audio-wma',
            'title' => 'WMA',
            'description' => 'Windows Media Audio',
            'extensions' => ['.wma'],
            'type' => 'audio'
        ],
        'audio/vnd.wave' => [
            'icon' => 'icon-audio-wav',
            'title' => 'WAV',
            'description' => 'Waveform Audio File Format',
            'extensions' => ['.wav'],
            'type' => 'audio'
        ],
        'audio/vnd.rn-realaudio' => [
            'icon' => 'icon-audio-rm',
            'title' => 'RM',
            'description' => 'RealMedia',
            'extensions' => ['.rm', '.ram', '.rmvb'],
            'type' => 'audio'
        ],
        'audio/x-matroska' => [
            'icon' => 'icon-audio-mka',
            'title' => 'MKA',
            'description' => 'Matroska Audio File',
            'extensions' => ['.mka'],
            'type' => 'audio'
        ],
        'application/pdf' => [
            'icon' => 'icon-document-pdf',
            'title' => 'PDF',
            'description' => 'Adobe Portable Document Format',
            'extensions' => ['.pdf'],
            'type' => 'document'
        ],
        'application/msword' => [
            'icon' => 'icon-document-doc',
            'title' => 'MS Word',
            'description' => 'Microsoft Office Word',
            'extensions' => ['.doc', '.docx'],
            'type' => 'document'
        ],
        'application/vnd.oasis.opendocument.text' => [
            'icon' => 'icon-document-odt',
            'title' => 'ODF',
            'description' => 'OpenDocument Text',
            'extensions' => ['.odt'],
            'type' => 'document'
        ],
        'application/vnd.ms-excel' => [
            'icon' => 'icon-document-xls',
            'title' => 'MS Excel',
            'description' => 'Microsoft Office Excel',
            'extensions' => ['.xls', '.xlsx'],
            'type' => 'document'
        ],
        'application/vnd.oasis.opendocument.spreadsheet' => [
            'icon' => 'icon-document-ods',
            'title' => 'ODS',
            'description' => 'OpenDocument Spreadsheet',
            'extensions' => ['.ods'],
            'type' => 'document'
        ],
        'application/vnd.ms-powerpoint' => [
            'icon' => 'icon-document-ppt',
            'title' => 'MS PowerPoint',
            'description' => 'Microsoft Office PowerPoint',
            'extensions' => ['.ppt'],
            'type' => 'document'
        ],
        'application/vnd.oasis.opendocument.presentation' => [
            'icon' => 'icon-document-odp',
            'title' => 'ODP',
            'description' => 'OpenDocument Presentation',
            'extensions' => ['.odp'],
            'type' => 'document'
        ],
        'application/rtf' => [
            'icon' => 'icon-document-rtf',
            'title' => 'RTF',
            'description' => 'Rich Text Format',
            'extensions' => ['.rtf'],
            'type' => 'document'
        ],
        'application/json' => [
            'icon' => 'icon-document-json',
            'title' => 'JSON',
            'description' => 'JavaScript Object Notation',
            'extensions' => ['.json'],
            'type' => 'document'
        ],
        'text/csv' => [
            'icon' => 'icon-document-csv',
            'title' => 'CSV',
            'description' => 'Comma-Separated Values',
            'extensions' => ['.csv'],
            'type' => 'document'
        ],
        'text/plain' => [
            'icon' => 'icon-document-txt',
            'title' => 'TXT',
            'description' => 'Plain Text',
            'extensions' => ['.txt'],
            'type' => 'document'
        ]
    ];

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
    public function dashboardNavItems($options = false)
    {
        $items = [
            'label' => $this->name,
            'url' => [$this->routePrefix . '/'. $this->id],
            'icon' => 'fa fa-fw fa-icons',
            'active' => in_array(\Yii::$app->controller->module->id, [$this->id]),
            'items' => [
                [
                    'label' => Yii::t('app/modules/media', 'Library'),
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

    public function getAllowedMime($asArray = true)
    {
        $list = [];
        foreach ($this->allowedMime as $mime => $value) {
            if ($value === true)
                $list[] = $mime;
        }

        if ($asArray)
            return $list;
        else
            return implode(", ", $list);

    }

    public function getMimeTypes($asArray = true)
    {
        $list = $this->mimeTypes;

        if ($asArray)
            return $list;
        else
            return implode(", ", $list);

    }

    public function getAllowedExtensions($asArray = true)
    {
        $list = [];
        foreach ($this->allowedMime as $mime => $value) {
            if ($value === true && isset($types[$mime])) {
                if (isset($types[$mime]['extensions'])) {
                    if ($extensions = $types[$mime]['extensions']) {
                        foreach ($extensions as $extension) {
                            $list[] = $extension;
                        }
                    }
                }

            }
        }

        if ($asArray)
            return $list;
        else
            return str_replace(".", "", implode(", ", $list));

    }

    /**
     * @return array
     */
    public function getMimeTypesList($allTypes = false)
    {
        $list = [];
        if ($allTypes)
            $list['*'] = Yii::t('app/modules/media', 'All types');

        if ($types = $this->getMimeTypes()) {
            $allowed = $this->getAllowedMime();
            foreach ($types as $mime => $value) {
                if (in_array($mime, $allowed)) {
                    $list[$mime] = $value['title'];
                }
            }
        }

        return $list;
    }

    public function getTypeByMime($mime = null) {
        if (!is_null($mime)) {
            $types = $this->getMimeTypes();
            if (isset($types[$mime])) {
                if ($mime = $types[$mime]) {
                    return $mime;
                }
            }
        }

        return null;
    }

    /**
     * Returns the allowed maximum size of uploaded files.
     *
     * @param bool $formatted
     * @return int, size in bytes
     */
    public function getMaxUploadLimit($formatted = false)
    {
        $max_file_size = null;
        if (!$this->isConsole() && Yii::$app->request->isPost)
            $max_file_size = (int)Yii::$app->request->post('MAX_FILE_SIZE');

        $max_upload_limit = StringHelper::sizeToBytes(\ini_get('upload_max_filesize'));
        $max_post_limit = StringHelper::sizeToBytes(\ini_get('post_max_size'));

        if ($max_upload_limit > $max_post_limit)
            Yii::warning('PHP.ini\'s \'post_max_size\' is less than \'upload_max_filesize\'.', __METHOD__);

        if (isset($max_post_limit) && $max_post_limit < $max_upload_limit && $max_post_limit > 0)
            $limit = $max_post_limit;

        if (isset($this->maxUploadSize) && $this->maxUploadSize < $limit && $this->maxUploadSize > 0)
            $limit = $this->maxUploadSize;

        if (isset($max_file_size) && $max_file_size < $limit && $max_file_size > 0)
            $limit = $max_file_size;

        if ($formatted)
            return \Yii::$app->formatter->asShortSize($limit);

        return $limit;
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