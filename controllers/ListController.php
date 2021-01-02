<?php

namespace wdmg\media\controllers;

use wdmg\helpers\StringHelper;
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
                    'upload' => ['GET', 'POST'],
                    'batch' => ['POST'],
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
        if (!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        } else if ($this->module->moduleExist('admin/rbac')) { // Ok, then we check access according to the rules
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['update', 'create', 'delete', 'batch'],
                        'roles' => ['updatePosts'],
                        'allow' => true
                    ], [
                        'roles' => ['viewDashboard'],
                        'allow' => true
                    ],
                ],
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

        if ($cat_id = Yii::$app->request->get('cat_id', null))
            $searchModel->cat_id = intval($cat_id);
        else
            $searchModel->cat_id = '*';

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'module' => $this->module,
            'model' => $model
        ]);
    }

    /**
     * Updates an existing Media item model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Get current URL before save this media item
        $oldMediaUrl = $model->getMediaUrl(false);

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                return $this->asJson(['success' => $success, 'alias' => $model->alias, 'errors' => $model->errors]);
            }
        } else {
            if ($model->load(Yii::$app->request->post())) {

                // Get new URL for saved media item
                $newMediaUrl = $model->getMediaUrl(false);

                if ($model->save()) {

                    // Set 301-redirect from old URL to new
                    if (isset(Yii::$app->redirects) && ($oldMediaUrl !== $newMediaUrl) && ($model->status == $model::MEDIA_STATUS_PUBLISHED)) {
                        // @TODO: remove old redirects
                        Yii::$app->redirects->set('media', $oldMediaUrl, $newMediaUrl, 301);
                    }

                    // Log activity
                    $this->module->logActivity(
                        'Media item `' . $model->name . '` with ID `' . $model->id . '` has been successfully updated.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t(
                            'app/modules/media',
                            'OK! Media item `{name}` successfully updated.',
                            [
                                'name' => $model->name
                            ]
                        )
                    );
                } else {
                    // Log activity
                    $this->module->logActivity(
                        'An error occurred while update the media item `' . $model->name . '` with ID `' . $model->id . '`.',
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/media',
                            'An error occurred while update a media item `{name}`.',
                            [
                                'name' => $model->name
                            ]
                        )
                    );
                }
                return $this->redirect(['list/index']);
            }
        }

        return $this->render('update', [
            'module' => $this->module,
            'model' => $model
        ]);
    }

    /**
     * Displays a single Media item post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'module' => $this->module,
            'model' => $model
        ]);
    }

    public function actionUpload()
    {

        $model = new Media();
        if (Yii::$app->request->isPost || Yii::$app->request->isAjax) {

            $saved = 0;
            $post = Yii::$app->request->post();
            $response = [];
            $files = UploadedFile::getInstances($model, 'files');

            if (!empty($files)) {
                foreach ($files as $file) {
                    if ($file->error == 0) {
                        $media = new Media();
                        if (is_null($media->name))
                            $media->name = $file->baseName;

                        if (is_null($media->cat_id))
                            $media->cat_id = Categories::DEFAULT_CATEGORY_ID;

                        if ($media->load(Yii::$app->request->post())) {
                            if ($media->upload($file)) {
                                if ($media->save()) {
                                    $response[$file->name]['status'] = true;
                                    $saved++;
                                    continue;
                                }
                            }
                            $response[$file->name] = [
                                'status' => false,
                                'errors' => $media->errors,
                            ];
                        }
                    } else {
                        $response[$file->name] = [
                            'status' => false,
                            'errors' => $file->error,
                        ];
                    }
                }
            }

            if ($saved) {
                // Log activity
                $this->module->logActivity(
                    $saved . ' media item(s) successfully added.',
                    $this->uniqueId . ":" . $this->action->id,
                    'success',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'success',
                    Yii::t(
                        'app/modules/media',
                        'OK! {count, number} media {count, plural, one{item} few{items} other{items}} successfully {count, plural, one{added} few{added} other{added}}.',
                        [
                            'count' => $saved
                        ]
                    )
                );
            } else {
                // Log activity
                $this->module->logActivity(
                    'An error occurred while added a media item(s)',
                    $this->uniqueId . ":" . $this->action->id,
                    'danger',
                    1
                );

                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/media',
                        'An error occurred while added a media item(s).'
                    )
                );
            }

            if (Yii::$app->request->isAjax) {
                return $this->asJson($response);
            }

        } else {
            return $this->render('upload', [
                'model' => $model
            ]);
        }

        return $this->redirect(['list/index']);
    }

    /**
     */
    public function actionBatch($action = null, $attribute = null, $value = null)
    {
        if (Yii::$app->request->isPost) {
            $selection = Yii::$app->request->post('selected', null);
            if (!is_null($selection)) {
                if ($action == 'change') {

                    if ($attribute == 'status') {
                        $updated = Media::updateAll(['status' => intval($value)], ['id' => $selection]);
                    } elseif ($attribute == 'cat_id') {
                        $updated = Media::updateAll(['cat_id' => intval($value)], ['id' => $selection]);
                    }

                    if ($updated) {
                        // Log activity
                        $this->module->logActivity(
                            $updated . ' media item(s) successfully updated.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );

                        Yii::$app->getSession()->setFlash(
                            'success',
                            Yii::t(
                                'app/modules/media',
                                'OK! {count, number} media {count, plural, one{item} few{items} other{items}} successfully {count, plural, one{updated} few{updated} other{updated}}.',
                                [
                                    'count' => $updated
                                ]
                            )
                        );
                    } else {
                        // Log activity
                        $this->module->logActivity(
                            'An error occurred while updating a media item(s).',
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );

                        Yii::$app->getSession()->setFlash(
                            'danger',
                            Yii::t(
                                'app/modules/media',
                                'An error occurred while updating a media item(s).'
                            )
                        );
                    }

                } elseif ($action == 'delete') {

                    $deleted = 0;
                    $models = Media::findAll(['id' => $selection]);
                    foreach($models as $model) {
                        if ($model->delete())
                            $deleted++;
                    }

                    if ($deleted) {
                        // Log activity
                        $this->module->logActivity(
                            $deleted . ' media item(s) successfully deleted.',
                            $this->uniqueId . ":" . $this->action->id,
                            'success',
                            1
                        );

                        Yii::$app->getSession()->setFlash(
                            'success',
                            Yii::t(
                                'app/modules/media',
                                'OK! {count, number} media {count, plural, one{item} few{items} other{items}} successfully {count, plural, one{deleted} few{deleted} other{deleted}}.',
                                [
                                    'count' => $deleted
                                ]
                            )
                        );
                    } else {
                        // Log activity
                        $this->module->logActivity(
                            'An error occurred while deleting a media item(s).',
                            $this->uniqueId . ":" . $this->action->id,
                            'danger',
                            1
                        );

                        Yii::$app->getSession()->setFlash(
                            'danger',
                            Yii::t(
                                'app/modules/media',
                                'An error occurred while deleting a media item(s).'
                            )
                        );
                    }
                }
            }
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
        if ($model->delete()) {

            // @TODO: remove redirects of deleted pages

            // Log activity
            $this->module->logActivity(
                'Media item `' . $model->name . '` with ID `' . $model->id . '` has been successfully deleted.',
                $this->uniqueId . ":" . $this->action->id,
                'success',
                1
            );

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
            // Log activity
            $this->module->logActivity(
                'An error occurred while deleting the media item `' . $model->name . '` with ID `' . $model->id . '`.',
                $this->uniqueId . ":" . $this->action->id,
                'danger',
                1
            );

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
     * Finds the Media item model based on its primary key value.
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

        throw new NotFoundHttpException(Yii::t('app/modules/media', 'The requested media item does not exist.'));
    }
}
