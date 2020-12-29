<?php

namespace wdmg\media\models;

use Yii;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\imagine\Image;
use yii\base\InvalidArgumentException;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use wdmg\validators\JsonValidator;
use wdmg\media\models\Categories;

/**
 * This is the model class for table "{{%media}}".
 *
 * @property int $id
 * @property int $cat_id
 * @property string $name
 * @property string $alias
 * @property string $path
 * @property int $size
 * @property string $title
 * @property string $caption
 * @property string $alt
 * @property string $description
 * @property string $mime_type
 * @property string $params
 * @property string $reference
 * @property boolean $status
 * @property string $created_at
 * @property integer $created_by
 * @property string $updated_at
 * @property integer $updated_by
 */
class Media extends ActiveRecord
{
    public $route;

    const MEDIA_STATUS_DRAFT = 0; // Media has draft
    const MEDIA_STATUS_PUBLISHED = 1; // Media has been published

    public $files;
    public $url;
    private $module;

    public function init()
    {
        parent::init();
        if (!$this->module = Yii::$app->getModule('admin/media'))
            $this->module = Yii::$app->getModule('media');

    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                ],
                'value' => new Expression('NOW()'),
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'updated_by',
            ],
            'sluggable' =>  [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'alias',
                'ensureUnique' => true,
                'skipOnEmpty' => true,
                'immutable' => true,
                'value' => function ($event) {
                    return mb_substr($this->name, 0, 32);
                }
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        $rules = [
            [['name', 'alias', 'cat_id', 'path'], 'required'],
            [['name', 'alias', 'mime_type'], 'string', 'min' => 2, 'max' => 128],
            [['path', 'title', 'alt', 'reference'], 'string', 'max' => 255],
            [['caption'], 'string', 'max' => 550],
            [['description'], 'string'],
            [['mime_type'], 'in', 'range' => $this->module->getAllowedMime()],
            [['cat_id', 'size'], 'integer'],
            [['files'], 'file', 'skipOnEmpty' => true,
                'maxFiles' => $this->module->maxFilesToUpload,
                'maxSize' => $this->module->getMaxUploadLimit(false),
                'extensions' => $this->module->getAllowedExtensions(false),
                'checkExtensionByMimeType' => false
            ],
            [['params'], JsonValidator::class, 'message' => Yii::t('app/modules/media', 'The value of field `{attribute}` must be a valid JSON, error: {error}.')],
            [['status'], 'boolean'],
            ['alias', 'unique', 'message' => Yii::t('app/modules/media', 'Param attribute must be unique.')],
            ['alias', 'match', 'pattern' => '/^[A-Za-z0-9\-\_]+$/', 'message' => Yii::t('app/modules/media','It allowed only Latin alphabet, numbers and the «-», «_» characters.')],
            [['source', 'created_at', 'updated_at'], 'safe'],
        ];

        if (class_exists('\wdmg\users\models\Users')) {
            $rules[] = [['created_by', 'updated_by'], 'safe'];
        }

        return $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/modules/media', 'ID'),
            'cat_id' => Yii::t('app/modules/media', 'Category ID'),
            'name' => Yii::t('app/modules/media', 'Name'),
            'alias' => Yii::t('app/modules/media', 'Alias'),
            'files' => Yii::t('app/modules/media', 'Files'),
            'path' => Yii::t('app/modules/media', 'File path'),
            'size' => Yii::t('app/modules/media', 'File size'),
            'title' => Yii::t('app/modules/media', 'Title'),
            'caption' => Yii::t('app/modules/media', 'Caption'),
            'alt' => Yii::t('app/modules/media', 'Alternate'),
            'description' => Yii::t('app/modules/media', 'Description'),
            'mime_type' => Yii::t('app/modules/media', 'Mime type'),
            'params' => Yii::t('app/modules/media', 'Params'),
            'reference' => Yii::t('app/modules/media', 'Reference'),
            'status' => Yii::t('app/modules/media', 'Status'),
            'created_at' => Yii::t('app/modules/media', 'Created at'),
            'created_by' => Yii::t('app/modules/media', 'Created by'),
            'updated_at' => Yii::t('app/modules/media', 'Updated at'),
            'updated_by' => Yii::t('app/modules/media', 'Updated by'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();

        if (is_null($this->url))
            $this->url = $this->getUrl();

        if (is_array($this->params)) {
            $this->params = \yii\helpers\Json::encode($this->tags);
        }

    }

    public function beforeValidate()
    {
        if (is_null($this->cat_id))
            $this->cat_id = Categories::DEFAULT_CATEGORY_ID;

        if (is_string($this->params) && JsonValidator::isValid($this->params)) {
            $this->params = \yii\helpers\Json::decode($this->params);
        } elseif (is_array($this->params)) {
            $this->params = \yii\helpers\Json::encode($this->params);
        }

        return parent::beforeValidate();
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {

        if (is_string($this->params) && JsonValidator::isValid($this->params)) {
            $this->params = \yii\helpers\Json::decode($this->params);
        }

        // Set default category if category not be selected
        if ($insert && empty($this->cat_id))
            $this->cat_id = [1];

        return parent::beforeSave($insert);
    }

    /**
     * @return string
     */
    public function getMediaPath($absoluteUrl = false)
    {

        if (isset(Yii::$app->params["media.mediaPath"])) {
            $mediaPath = Yii::$app->params["media.mediaPath"];
        } else {

            if (!$module = Yii::$app->getModule('admin/media'))
                $module = Yii::$app->getModule('media');

            $mediaPath = $module->mediaPath;
        }

        if ($absoluteUrl)
            return \yii\helpers\Url::to(str_replace('\\', '/', $mediaPath), true);
        else
            return $mediaPath;

    }

    /**
     * @return string
     */
    public function getMediaThumbsPath($absoluteUrl = false)
    {

        if (isset(Yii::$app->params["media.mediaThumbsPath"])) {
            $mediaThumbsPath = Yii::$app->params["media.mediaThumbsPath"];
        } else {

            if (!$module = Yii::$app->getModule('admin/media'))
                $module = Yii::$app->getModule('media');

            $mediaThumbsPath = $module->mediaThumbsPath;
        }

        if ($absoluteUrl)
            return \yii\helpers\Url::to(str_replace('\\', '/', $mediaThumbsPath), true);
        else
            return $mediaThumbsPath;

    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'created_by']);
        else
            return $this->created_by;
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        if (class_exists('\wdmg\users\models\Users'))
            return $this->hasOne(\wdmg\users\models\Users::class, ['id' => 'updated_by']);
        else
            return $this->updated_by;
    }

    /**
     * Returns published media items
     *
     * @param null $cond sampling conditions
     * @param bool $asArray flag if necessary to return as an array
     * @return array|ActiveRecord|null
     */
    public function getPublished($cond = null, $asArray = false) {
        if (!is_null($cond) && is_array($cond))
            $models = self::find()->where(ArrayHelper::merge($cond, ['status' => self::MEDIA_STATUS_PUBLISHED]));
        elseif (!is_null($cond) && is_string($cond))
            $models = self::find()->where(ArrayHelper::merge([$cond], ['status' => self::MEDIA_STATUS_PUBLISHED]));
        else
            $models = self::find()->where(['status' => self::MEDIA_STATUS_PUBLISHED]);

        if ($asArray)
            return $models->asArray()->all();
        else
            return $models->all();

    }

    /**
     * Returns all media posts (draft and published)
     *
     * @param null $cond sampling conditions
     * @param bool $asArray flag if necessary to return as an array
     * @return array|ActiveRecord|null
     */
    public function getAll($cond = null, $asArray = false) {
        if (!is_null($cond))
            $models = self::find()->where($cond);
        else
            $models = self::find();

        if ($asArray)
            return $models->asArray()->all();
        else
            return $models->all();

    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getCategories($cat_id = null, $asArray = false) {

        if (!($cat_id === false) && !is_integer($cat_id) && !is_string($cat_id))
            $cat_id = $this->cat_id;

        $query = Categories::find()->alias('cats');

        if (is_integer($cat_id))
            $query->andWhere([
                'id' => intval($cat_id)
            ]);

        if ($asArray)
            return $query->asArray()->all();
        else
            return $query->all();

    }

    /**
     * @return string
     */
    public function getRoute()
    {
        if (isset(Yii::$app->params["media.mediaRoute"])) {
            $route = Yii::$app->params["media.mediaRoute"];
        } else {

            if (!$module = Yii::$app->getModule('admin/media'))
                $module = Yii::$app->getModule('media');

            $route = $module->mediaRoute;
        }

        return $route;
    }

    /**
     *
     * @param $withScheme boolean, absolute or relative URL
     * @return string or null
     */
    public function getMediaUrl($withScheme = true, $realUrl = false)
    {
        $this->route = $this->getRoute();
        if (isset($this->alias)) {
            if ($this->status == self::MEDIA_STATUS_DRAFT && $realUrl)
                return \yii\helpers\Url::to(['default/view', 'alias' => $this->alias, 'draft' => 'true'], $withScheme);
            else
                return \yii\helpers\Url::to($this->route . '/' .$this->alias, $withScheme);

        } else {
            return null;
        }
    }

    /**
     * Returns the URL to the view of the current model
     *
     * @return string
     */
    public function getUrl()
    {
        if ($this->url === null)
            $this->url = $this->getMediaUrl();

        return $this->url;
    }

    /**
     * @return array
     */
    public function getStatusesList($allStatuses = false)
    {
        if ($allStatuses)
            return [
                '*' => Yii::t('app/modules/media', 'All statuses'),
                self::MEDIA_STATUS_DRAFT => Yii::t('app/modules/media', 'Draft'),
                self::MEDIA_STATUS_PUBLISHED => Yii::t('app/modules/media', 'Published'),
            ];
        else
            return [
                self::MEDIA_STATUS_DRAFT => Yii::t('app/modules/media', 'Draft'),
                self::MEDIA_STATUS_PUBLISHED => Yii::t('app/modules/media', 'Published'),
            ];
    }

    /**
     * @return array
     */
    public function getCategoriesList()
    {
        $list = [];
        if ($categories = $this->getCategories(null, true)) {
            $list = ArrayHelper::merge($list, ArrayHelper::map($categories, 'id', 'name'));
        }

        return $list;
    }

    /**
     * @return object of \yii\db\ActiveQuery
     */
    public function getAllCategories($cond = null, $select = ['id', 'name'], $asArray = false)
    {
        if ($cond) {
            if ($asArray)
                return Categories::find()->select($select)->where($cond)->asArray()->indexBy('id')->all();
            else
                return Categories::find()->select($select)->where($cond)->all();

        } else {
            if ($asArray)
                return Categories::find()->select($select)->asArray()->indexBy('id')->all();
            else
                return Categories::find()->select($select)->all();
        }
    }

    /**
     * @return array
     */
    public function getAllCategoriesList($allCategories = false)
    {
        $list = [];
        if ($allCategories)
            $list['*'] = Yii::t('app/modules/media', 'All categories');

        if ($categories = $this->getAllCategories(null, ['id', 'name'], true)) {
            $list = ArrayHelper::merge($list, ArrayHelper::map($categories, 'id', 'name'));
        }

        return $list;
    }

    /**
     * @param null $file
     * @return bool|string
     * @throws \yii\base\Exception
     */
    public function upload($file = null)
    {
        if (!$file)
            return false;

        // Get base path for storage media
        $basepath = Yii::getAlias('@webroot') . $this->getMediaPath();
        $path = $this->getMediaPath();

        // Create the folder if not exist
        if (FileHelper::createDirectory($basepath, $mode = 0775, $recursive = true)) {

            // Generate full path with year and month
            $savepath = $basepath . "/" . date('Y') . "/" . date('m');
            $fullpath = $path . "/" . date('Y') . "/" . date('m');

            if (FileHelper::createDirectory($savepath, $mode = 0775, $recursive = true)) {


                // Generate filename of media
                $filename = $file->baseName . "." . $file->extension;
                $org_path = $savepath . "/" . $filename;
                $web_path = $fullpath . "/" . $filename;

                // Check file not exists or generate unique filename
                $i = 1;
                while (file_exists($org_path) && $i < 999999) {
                    $filename = $file->baseName . $file->baseName . "-$i" . "." . $file->extension;
                    $org_path = $savepath . "/" . $filename;
                    $web_path = $fullpath . "/" . $filename;
                    $i++;
                }

                $this->path = $web_path;

                if ($file->type)
                    $this->mime_type = $file->type;
                else
                    $this->mime_type = FileHelper::getMimeType($org_path);

                $this->size = $file->size;

                if ($this->validate()) {
                    if ($file->saveAs($org_path)) {

                        if ($mime = $this->module->getTypeByMime($this->mime_type)) {
                            if (isset($mime['type']) && in_array($file->extension, [
                                    'jpg', 'jpeg', 'gif', 'png', 'wbmp', 'xbm', 'webp', 'bmp'
                                ])) {
                                if ($mime['type'] == 'image') {
                                    $thumbpath = Yii::getAlias('@webroot') . $this->getMediaThumbsPath();
                                    if (\yii\helpers\FileHelper::createDirectory($thumbpath, $mode = 0775, $recursive = true)) {
                                        $thumbnail = $thumbpath . "/" . md5($this->path) . ".jpg";
                                        Image::thumbnail($org_path, 480, 360)->save($thumbnail, ['quality' => 75]);
                                    }
                                }
                            }
                        }

                        return $filename;
                    }
                }
            }
        }

        return false;
    }

    public function getSource($asWebURL = true, $checkFileExists = false) {

        $filepath = Yii::getAlias('@web') . $this->path;
        $realpath = Yii::getAlias('@webroot') . $this->path;
        $source = (($asWebURL) ? $filepath : $realpath);

        if ($checkFileExists) {
            if (file_exists($realpath))
                return $source;
            else
                return false;
        } else {
            return $source;
        }
    }

    public function getThumbnail($asWebURL = true, $checkFileExists = false) {

        $thumbpath = Yii::getAlias('@web') . $this->getMediaThumbsPath();
        $realpath = Yii::getAlias('@webroot') . $this->getMediaThumbsPath();

        if ($asWebURL)
            $thumbnail = $thumbpath . "/" . md5($this->path) . ".jpg";
        else
            $thumbnail = $realpath . "/" . md5($this->path) . ".jpg";

        if ($checkFileExists) {
            if (file_exists($realpath . "/" . md5($this->path) . ".jpg"))
                return $thumbnail;
            else
                return false;
        } else {
            return $thumbnail;
        }
    }

    /**
     * Returns the allowed maximum size of uploaded files.
     *
     * @param bool $formatted
     * @return int|string|null
     */
    public function getMaxUploadFilesize($formatted = false)
    {
        $limit = $this->module->getMaxUploadLimit();
        if ($formatted)
            return \Yii::$app->formatter->asShortSize($limit);

        return $limit;
    }

    public function delete()
    {
        if ($filename = $this->getSource(false, true))
            FileHelper::unlink($filename);

        if ($thumbnail = $this->getThumbnail(false, true))
            FileHelper::unlink($thumbnail);

        return parent::delete();
    }

}
