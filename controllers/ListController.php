<?php

namespace wdmg\media\controllers;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use wdmg\media\models\Media;
use wdmg\media\models\MediaSearch;
use wdmg\media\models\Categories;

/**
 * ListController implements the CRUD actions for Media model.
 */
class ListController extends Controller
{

    /**
     * {@inheritdoc}
     */
    public $defaultAction = 'index';

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ]
        ];

        // If auth manager not configured use default access control
        if(!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        }

        return $behaviors;
    }

    /**
     * Lists of all media items
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Media();
        $searchModel = new MediaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'module' => $this->module,
            'model' => $model
        ]);
    }


    public function actionUpload()
    {
        $model = new Media();
        if (Yii::$app->request->isPost) {

            $files = UploadedFile::getInstances($model, 'files');
            if (is_array($files)) {
                foreach ($files as $file) {
                    if ($file->error == 0) {

                        $media = new Media();
                        if (is_null($media->name))
                            $media->name = $file->name;

                        if (is_null($media->cat_id))
                            $media->cat_id = Categories::DEFAULT_CATEGORY_ID;

                        if ($media->load(Yii::$app->request->post()) && $media->upload($file)) {
                            if ($media->validate()) {
                                $media->save();
                            }/* else {

                            }*/
                        }
                    }
                }
            }
        } else {
            return $this->render('upload', [
                'model' => $model,
            ]);
        }

        return $this->redirect(['list/index']);
    }

    /**
     * Deletes an existing Media item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {

        $model = $this->findModel($id);
        if($model->delete()) {

            // @TODO: remove redirects of deleted pages

            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t(
                    'app/modules/media',
                    'OK! Media item `{name}` successfully deleted.',
                    [
                        'name' => $model->name
                    ]
                )
            );
        } else {
            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t(
                    'app/modules/media',
                    'An error occurred while deleting a media item `{name}`.',
                    [
                        'name' => $model->name
                    ]
                )
            );
        }

        return $this->redirect(['list/index']);
    }

    /**
     * Finds the Blog post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return media model item
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Media::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/modules/blog', 'The requested media item does not exist.'));
    }
}
